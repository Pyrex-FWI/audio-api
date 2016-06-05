<?php

namespace AppBundle\Service;

use AudioCoreEntity\Entity\Genre;
use Deejay\Id3ToolBundle\Wrapper\Id3Manager;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\VarDumper\VarDumper;

class TempDir
{
    /** @var  \SplFileInfo */
    private $tempDirectory;
    /** @var  \SplFileInfo */
    private $rootDirectory;
    /** @var  SplFileInfo[] */
    private $directoryFiles;
    /** @var  bool */
    private $strictMode = false;
    /** @var bool  */
    private $countFilesFromSvf = false;
    private $errors = [];

    /** @var  id3Manager */
    private $id3Manager;
    private $tempDirectoryId3Genres = [];
    private $tempDirectoryId3Years = [];
    private $tempDirectoryId3Albums = [];
    private $tempDirectoryId3Artists = [];

    public function __construct(Id3Manager $id3Manager)
    {
        $this->id3Manager = $id3Manager;
    }


    /**
     * @param $getTempDir
     * @return $this
     */
    public function setTempDirectory($getTempDir)
    {
        $this->errors = [];
        $this->tempDirectory = new \SplFileInfo($getTempDir);
        $this->directoryFiles =(iterator_to_array(Finder::create()->depth('== 0')->in($this->getTempDirectory()->getRealPath())->files()->getIterator()));

        return $this;
    }

    /**
     * @param $rootTemp
     * @return $this
     */
    public function setRootDirectory($rootTemp)
    {
        $this->rootDirectory = new \SplFileInfo($rootTemp);

        return $this;
    }

    /**
     * @return \SplFileInfo
     */
    private function getTempDirectory()
    {
        return $this->tempDirectory;
    }

    public function readTempDirectoryMeta()
    {
        $genres     = [];
        $years      = [];
        $albums     = [];
        $artists    = [];
        $filesToCheck = ($this->id3Manager->keepReadablesFiles(array_keys($this->directoryFiles)));
        if (count($filesToCheck) == 0) {
            return false;
        }
        $result = $this->id3Manager->readMultipleTags($filesToCheck);
        $nbResult = count($result);

        for ($i = 0; $i < $nbResult; $i++) {
            $genres = array_merge($genres, $this->id3Manager->getReaderWrapper()->eq($i)->getGenres());
            $years[] = $this->id3Manager->getReaderWrapper()->eq($i)->getYear();
            $albums[] = $this->id3Manager->getReaderWrapper()->eq($i)->getAlbum();
            $artists[] = $this->id3Manager->getReaderWrapper()->eq($i)->getArtist();
        }
        $this->tempDirectoryId3Genres   = array_filter(array_unique($genres), 'strlen');
        $this->tempDirectoryId3Years    = array_filter(array_unique($years), 'strlen');
        $this->tempDirectoryId3Albums   = array_filter(array_unique($albums), 'strlen');
        $this->tempDirectoryId3Artists  = array_filter(array_unique($artists), 'strlen');
        return true;
    }

    public function setStrictMode($val)
    {
        $this->strictMode = $val;
        return $this;
    }

    /**
     * @param $path
     * @return bool
     */
    private function containNoEmptySubDirs($path)
    {
        //$haveSubDirs =  Finder::create()->in($path)->directories()->count() > 0 ? true : false;
        $notEmpty = Finder::create()->depth('>=1')->in($path)->files()->size( '>= 1Mi')->count() > 0 ? true : false;
        //dump(iterator_to_array(Finder::create()->depth('>=1')->in($path)->files()->size( '>= 1Mi')->getIterator()));
        return $notEmpty;
    }

    public  function canBeMoved()
    {
        if ($this->strictMode && $this->containNoEmptySubDirs($this->getTempDirectory()->getRealPath())) {
            $this->addError('Path contain no empty subdir');
            return false;
        }
        
        if (count($this->getTempDirectoryId3Genres()) == 0 || count($this->getTempDirectoryId3Years()) == 0) {
            $this->addError('No Genre or No Year');
            return false;
        }

        if (!$this->genreIsUnique() || !$this->yearIsUnique() || !$this->albumIssUnique()) {
            $this->addError('Multiple Genres, Year or Album name');
            return false;
        }

        return true;
    }

    /**
     * @return array
     */
    public function getTempDirectoryId3Genres()
    {
        return $this->tempDirectoryId3Genres;
    }

    /**
     * @return array
     */
    public function getTempDirectoryId3Years()
    {
        return $this->tempDirectoryId3Years;
    }

    /**
     * @return array
     */
    public function getTempDirectoryId3Albums()
    {
        return $this->tempDirectoryId3Albums;
    }

    public function genreIsUnique()
    {
        return count($this->getTempDirectoryId3Genres()) == 1;
    }

    public function yearIsUnique()
    {
        return count($this->getTempDirectoryId3Years()) == 1;
    }

    public function albumIssUnique()
    {
        return count($this->getTempDirectoryId3Albums()) == 1;
    }

    /**
     * @return \SplFileInfo
     */
    public function getRootDirectory()
    {
        return $this->rootDirectory;
    }

    public function move()
    {
        //Override Already exist?
        if ($this->copyTempToRoot()) {
            $this->removeTempDir();
            $this->removeParentTempDirFolderIFEmpty();
        }
    }

    /**
     * @return string
     */
    public function getNewPath()
    {
        return $newDir = (sprintf(
            '%s/%s/%s/',
            $this->getRootDirectory()->getRealPath(),
            str_replace('/', ' - ',$this->getTempDirectoryId3Genres()[0]),
            $this->getTempDirectoryId3Years()[0]
        ));
    }

    /**
     * @return mixed
     */
    private function copyTempToRoot()
    {
        $newDir = $this->getNewPath();

        if (!is_dir($newDir)) {
            mkdir($newDir, 0765, true);
        }

        $command = sprintf('cp -rf %s %s', escapeshellarg($this->getTempDirectory()->getRealPath()), escapeshellarg($newDir));
        exec($command, $out, $returnVar);
        return !boolval($returnVar);
    }

    private function removeTempDir()
    {
        $command = "rm -rf ". escapeshellarg($this->getTempDirectory()->getRealPath());
        exec($command, $out, $returnVar);
        return !boolval($returnVar);
    }

    /**
     * @return boolean
     */
    public function getCountFilesFromSvf()
    {
        return $this->countFilesFromSvf;
    }

    /**
     * @param boolean $countFileFromSvf
     * @return TempDir
     */
    public function setCountFilesFromSvf($countFileFromSvf)
    {
        $this->countFilesFromSvf = $countFileFromSvf;
        return $this;
    }

    private function addError($string)
    {
        $this->errors[] = $string;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    private function removeParentTempDirFolderIFEmpty()
    {
        if( count(scandir($this->getTempDirectory()->getPath())) < 3) {
            $command = "rm -rf ". escapeshellarg($this->getTempDirectory()->getPath());
            exec($command, $out, $returnVar);
        }
    }

    public function getTempDirectoryId3Artists()
    {
        return $this->tempDirectoryId3Artists;
    }

}