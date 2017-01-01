<?php

/*
 * This file is part of the Audio Api.
 *
 * (c) Christophe Pyree
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Command;

use AppBundle\Entity\Media;
use AppBundle\Organizer\MediaOrganizerManager;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class RemapLocalFileCommand extends ContainerAwareCommand
{
    private $notExist;
    private $total = 0;
    private $inputDir;
    /** @var OutputInterface */
    private $output;
    /** @var InputInterface */
    private $input;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('media:remapByFileName:local-files')
            ->setDescription('Reorganize files')
            //->addArgument('providerName', InputArgument::REQUIRED)
            //->addArgument('rules', InputArgument::REQUIRED)
            ->addArgument('inputDir', InputArgument::REQUIRED)
            ->addOption('dry', InputArgument::OPTIONAL)
            ->setHelp(<<<'EOF'
The <info>%command.name%</info> command greets somebody or everybody:

<info>php %command.full_name%</info>

The optional argument specifies who to greet:

<info>php %command.full_name%</info> Fabien
EOF
            );
    }

    /**
     * @return MediaOrganizerManager
     */
    public function getManager()
    {
        return $this->getContainer()->get('app.media.organizer.manager');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->init($input, $output);

        /** @var Registry $doctrine */
        $doctrine = $this->getContainer()->get('doctrine');

        /* @var \AppBundle\Organizer\MediaOrganizerManager $fileOrganizer */
        /* @var EntityRepository $mediaRepository */
        $this->mediaRepository = $doctrine->getRepository('\AppBundle\Entity\Media');
        /* @var EntityManager $em */
        $this->em = $doctrine->getManager();
        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);

        $files = Finder::create()->in($this->inputDir)->name('/\.mp(3|4)$/')->files();
        $this->total = $files->count();
        $size = 1000;

        $output->writeln($this->inputDir);

        $this->progressBar = new ProgressBar($output, $this->total);
        ProgressBar::setFormatDefinition(
            'debug',
            "%current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%\n");
        $this->progressBar->setFormat('debug');

        $queue = [];
        foreach ($files as $file) {
            $this->progressBar->advance();
            $queue[$file->getBasename()] = $file;
            if (count($queue) === $size) {
                $this->remapByFileName($queue);
                $queue = [];
            }
        }
        $this->remapByFileName($queue);
        $this->printSummary();
    }

    /**
     * @param SplFileInfo[] $queue
     */
    public function remapByFileName($queue)
    {
        /* @var SplFileInfo $file */
        /* @var Media $fileInDb */
        $filesInDb = $this->mediaRepository->findBy(['fileName' => array_keys($queue)]);
        foreach ($filesInDb as $fileInDb) {
            if (!isset($queue[$fileInDb->getFileName()])) {
                $this->notExist[] = $fileInDb->getFileName();
                continue;
            }
            $file = $queue[$fileInDb->getFileName()];
            $fileInDb->setFullPath($file->getRealPath());
            $fileInDb->setExist(true);
            $this->em->persist($fileInDb);
        }
        $this->em->flush();
        $this->em->clear();
    }

    private function printSummary()
    {
        $this->output->writeln('<info>Summary</info>');
        if (count($this->notExist) > 0) {
            $this->showMissingSample();
        }
        $tableHelper = new Table($this->output);
        $tableHelper->addRow(['Total', $this->total]);
        $tableHelper->addRow(['Missing files in Database', count($this->notExist)]);
        $tableHelper->render();
    }

    private function showMissingSample()
    {
        $tableHelper = new Table($this->output);
        $max = 50 >= count($this->notExist) ? count($this->notExist) : 50;
        for ($i = 0; $i < $max; ++$i) {
            $tableHelper->addRow([$this->notExist[$i]]);
        }
        $tableHelper->addRow(['Total', $this->total]);
        $tableHelper->render();
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @throws \Exception
     */
    private function init(InputInterface $input, OutputInterface $output)
    {
        $this->inputDir = $input->getArgument('inputDir');
        $this->output = $output;
        $this->input = $input;
    }
}
