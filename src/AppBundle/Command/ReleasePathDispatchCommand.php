<?php
/**
 * Copyright (c) 2016. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
 * Morbi non lorem porttitor neque feugiat blandit. Ut vitae ipsum eget quam lacinia accumsan.
 * Etiam sed turpis ac ipsum condimentum fringilla. Maecenas magna.
 * Proin dapibus sapien vel ante. Aliquam erat volutpat. Pellentesque sagittis ligula eget metus.
 * Vestibulum commodo. Ut rhoncus gravida arcu.
 */

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class ReleasePathDispatchCommand extends ContainerAwareCommand
{
    const ARG_PATHS = 'paths';
    const ARG_OUTPATH = 'outpath';
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('release:path:dispatch')
            ->addArgument(
                self::ARG_OUTPATH,
                InputArgument::REQUIRED,
                'Separate multiple paths with a space'
            )
            ->addArgument(
                self::ARG_PATHS,
                InputArgument::IS_ARRAY | InputArgument::REQUIRED,
                'Separate multiple paths with a space'
            )

            ->setDescription('Dispatch release directory according some rules');
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $paths = $input->getArgument(self::ARG_PATHS);
        $input->setArgument(self::ARG_PATHS, $this->pathsResolver($paths));
        $path = $input->getArgument(self::ARG_OUTPATH);
        $input->setArgument(self::ARG_OUTPATH, $this->pathsResolver($path));
    }

    private function pathsResolver($paths)
    {
        $resolved = [];
        $userPathExec = getcwd();

        foreach ((array) $paths as $path) {
            if (is_dir($userPathExec.DIRECTORY_SEPARATOR.$path)) {
                $resolved[] = rtrim($userPathExec, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$path;
                continue;
            }
            if (is_dir($path)) {
                $resolved[] = rtrim($path, DIRECTORY_SEPARATOR);
                continue;
            }

            throw new \Exception(sprintf('%s not exist', $path));
        }

        return is_array($paths) ? array_unique($resolved) : $resolved[0];
    }
    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $nfoMnager = $this->getContainer()->get('app.nfo_manager');
        $output = $input->getArgument(self::ARG_OUTPATH);
        $finder = Finder::create()->in($input->getArgument(self::ARG_PATHS))->directories()->depth(0);

        foreach ($finder as $fileInfo) {
            var_dump($fileInfo->getPathname());
            var_dump($nfoMnager->containNfo($fileInfo->getPathname()));
            if ($nfoMnager->containNfo($fileInfo->getPathname())) {
                $genre = $nfoMnager->getGenre($fileInfo->getPathname());

                var_dump($genre);
                if ($genre) {
                    $this->dispatchReleaseByGenre($fileInfo, $output, $genre);
                }
            }
        }
    }

    private function dispatchReleaseByGenre(SplFileInfo $fileInfo, $output, $genre)
    {
        $fs = new Filesystem();
        if (!is_dir(rtrim($output, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$genre.DIRECTORY_SEPARATOR)) {
            $fs->mkdir(rtrim($output, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$genre.DIRECTORY_SEPARATOR);
        }
        $dest = rtrim($output, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$genre.DIRECTORY_SEPARATOR.$fileInfo->getFilename();
        var_dump($fileInfo->getPathname());
        var_dump($dest);
        $fs->rename($fileInfo->getPathname(), $dest);
    }
}
