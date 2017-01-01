<?php

namespace AppBundle\Controller;

use AppBundle\Id3\Id3Manager;
use Cpyree\Id3\Metadata\Id3Metadata;
use OldSound\RabbitMqBundle\RabbitMq\Producer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class FolderController.
 *
 * @Route("/directory")
 */
class FolderController extends Controller
{
    /**
     * @param Request $request
     * @Route( name="directory_list")
     * @Method({"GET"})
     *
     * @return JsonResponse
     */
    public function getDirectories(Request $request)
    {
        $wkd = $this->safeWorkingDir($request->get('path') ? $request->get('path') : $this->getParameter('allowed_directories')[0]);
        $dirs = iterator_to_array(Finder::create()
            ->directories()
            ->sortByName()
            ->in($wkd)->depth(0)->getIterator());
        $dirs = array_map(function (\SplFileInfo $item) {
            return [
                'name' => $item->getFilename(),
                'pathName' => $item->getRealPath(),
                'expanded' => false,
                'childLoaded' => false,
                'isDir' => true,
            ];
        }, $dirs);

        $dirs = array_values($dirs);

        return new JsonResponse($dirs);
    }

    /**
     * @param Request $request
     * @Route("/content", name="directory_content")
     *
     * @return JsonResponse
     */
    public function getContentDir(Request $request)
    {
        $wkd = $this->safeWorkingDir($request->get('path') ? $request->get('path') : null);
        $files = iterator_to_array(Finder::create()
            ->files()
            ->name('/(mp3|flac)$/')
            ->sortByName()
            ->in($wkd)->depth(0)->getIterator());
        $files = array_map(function (\SplFileInfo $item) {
            return [
                'name' => $item->getFilename(),
                'pathName' => $item->getRealPath(),
                'isDir' => false,
            ];
        }, $files);

        $files = array_values($files);

        return new JsonResponse($files);
    }

    /**
     * @param Request $request
     * @Route("/get-dir-metadata", name="directory_get_meta")
     * @Method({"GET"})
     *
     * @return mixed
     */
    public function getDirMeta(Request $request)
    {
        $wkd = $this->safeWorkingDir($request->get('path') ? $request->get('path') : null);
        $files = iterator_to_array(Finder::create()
            ->files()
            ->name('/(mp3|flac)$/')
            ->in($wkd)->depth(0)->getIterator());

        /** @var Id3Manager $id3Manager */
        $id3Manager = $this->get('id3_manager');

        $files = array_map(function (\SplFileInfo $item) use ($id3Manager) {
            $id3m = new Id3Metadata($item->getRealPath());
            if ($id3Manager->read($id3m)) {
                return $id3m;
            }

            return;
        }, $files);

        $files = array_values($files);

        return new JsonResponse($files);
    }

    /**
     * @param Request $request
     * @Route("/set-dir-metadata", name="directory_set_meta")
     * @Method({"GET", "POST"})
     *
     * @return JsonResponse
     */
    public function setDirMeta(Request $request)
    {
        $wkd = $this->safeWorkingDir($request->get('path') ? $request->get('path') : null);
        $genre = $request->query->get('g');
        $year = $request->query->get('y');
        $files = iterator_to_array(Finder::create()
            ->files()
            ->name('/mp3|flac/')
            ->in($wkd)->depth(0)->getIterator());

        /** @var Id3Manager $id3Manager */
        $id3Manager = $this->get('id3_manager');

        foreach ($files as $file) {
            /* @var \SplFileInfo $file */

            $id3 = new Id3Metadata($file->getRealPath());
            if ($genre) {
                $id3->setGenre($genre);
            }
            if ($year) {
                $id3->setYear($year);
            }
            $id3Manager->write($id3);
        }

        return new JsonResponse([]);
    }

    /**
     * @Route("/move", name="directory_move")
     * @Method({"GET", "POST"})
     *
     * @return JsonResponse
     */
    public function moveAction(Request $request)
    {
        $wkd = $request->get('path');
        $return = false;
        if ($wkd && is_dir($wkd)) {
            /** @var Producer $saparFolderMoverProducer */
            $saparFolderMoverProducer = $this->get('old_sound_rabbit_mq.sapar_folder_move_producer');
            $saparFolderMoverProducer->publish($wkd);
            $this->get('monolog.logger.directory.move')->info(sprintf('add %s to sapar_move_queue', $wkd));
            $return = true;
        }

        return new JsonResponse($return);
    }

    /**
     * @param Request $request
     * @Route("/delete", name="directory_delete")
     * @Method({"GET", "DELETE"})
     *
     * @return JsonResponse
     */
    public function deleteAction(Request $request)
    {
        $wkd = $request->get('path');
        if ($wkd && is_dir($wkd)) {
            /** @var Producer $saparFolderMoverProducer */
            $saparFolderMoverProducer = $this->get('old_sound_rabbit_mq.sapar_folder_remove_producer');
            $saparFolderMoverProducer->publish($wkd);
            $this->get('monolog.logger.directory.remove')->info(sprintf('add %s to sapar_remove_queue', $wkd));
        }

        return new JsonResponse(true);
    }

    /**
     * @param Request $request
     * @Route("/add_into_ddp", name="directory_copy_to_ddp")
     * @Method({"GET", "post"})
     *
     * @return JsonResponse
     */
    public function ddpCopyFolderFileAction(Request $request)
    {
        $file = $request->get('file');
        $ddj = $this->getParameter('deejay_collection_path');
        if ($file && is_file($file)) {
            $splFile = new \SplFileInfo($file);
            $fs = new Filesystem();
            $fs->copy($splFile->getRealPath(), $ddj.DIRECTORY_SEPARATOR.$splFile->getFilename());
        }

        return new JsonResponse(true);
    }

    private function safeWorkingDir($path = null)
    {
        $wkd = realpath($this->getParameter('allowed_directories')[0]);

        if ($path) {
            $path = realpath($path);
            foreach ($this->getParameter('allowed_directories') as $aWkd) {
                $aWkd = realpath($aWkd);
                if (false !== strstr($path, $aWkd)) {
                    $wkd = $path;
                    break;
                }
            }
        }

        return $wkd;
    }

    private function isSubFolder($path)
    {
        foreach ($this->getParameter('allowed_directories') as $aWkd) {
        }
    }
}
