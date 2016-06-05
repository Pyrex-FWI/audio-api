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
use AppBundle\FileDumper\FileDumperReader;
use AppBundle\FileDumper\FileDumperWriter;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityRepository;
use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use OldSound\RabbitMqBundle\RabbitMq\Producer;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Cette commande permet de générer un dump des collections musicale.
 * Cette commande permet d'insérer/mettre à jour les fichiers collectés (dump) dans la base.
 * Cette command permet de mettre à jour les tags de la collection.
 *
 * @author Christophe PYREE <christophe.pyree@gmail.com>
 */
class CollectionDumperCommand extends ContainerAwareCommand
{
    /** @var LoggerInterface LoggerInterface */
    private $logger;
    /** @var  OutputInterface */
    private $output;
    /** @var  InputInterface */
    private $input;
    /** @var FileDumperWriter[] */
    private $fileDumperWriters = [];
    /** @var  ProgressBar */
    private $progressBar;

    /**
     * CollectionDumperCommand constructor.
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('media:dump:collection')->setDescription('Dump|Import|Update collection files into databases')
            ->addOption('dump',         null, InputOption::VALUE_NONE, 'This option dump all dirs registered into %collection.dirs% parameter', null)
            ->addOption('info',        null, InputOption::VALUE_NONE, 'Print all availablse FileDumpWriters', null)
            ->addOption('async-insert',    null, InputOption::VALUE_NONE, 'Send message to rabbit mq for database insertion', null)
            //->addOption('db-insert',    null, InputOption::VALUE_REQUIRED, 'db-insert=all db-insert=new', null)
            ->addOption('update-tags',  null, InputOption::VALUE_NONE)
            ->setHelp(
                <<<EOT
Dump your collection via %ddp.console.collection_dumper.shell_command%
EOT
            );
    }

    /**
     * Get all destination files name from FileDumper.
     *
     * @return array
     */
    private function getCollectionFileName()
    {
        $fileCollection = [];
        foreach ($this->fileDumperWriters as $dumpWriter) {
            /* @var \AppBundle\FileDumper\FileDumperWriter $dumpWriter */
            $fileCollection[$dumpWriter->getProvider()] = $dumpWriter->getFilePathName();
        }

        return $fileCollection;
    }
    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        ini_set('memory_limit', '-1');
        gc_enable();
        $this->input = $input;
        $this->output = $output;

        if ($this->input->getOption('info')) {
            $this->info();
        }

        if ($this->input->getOption('dump')) {
            $this->dumpCollections();
        }

        if ($this->input->getOption('async-insert')) {
            $this->produceMsg();
        }

        if ($this->input->getOption('update-tags')) {
            $this->updateTags();
        }
    }

    /**
     * this method is called by DI or CollectionDumperPass.
     *
     * @param FileDumperWriter $fileDumperWriter
     */
    public function addFileDumperWriter(FileDumperWriter $fileDumperWriter)
    {
        $this->fileDumperWriters[$fileDumperWriter->getName()] = $fileDumperWriter;
    }

    /**
     * Find media and Write dump file.
     */
    private function dumpCollections()
    {
        foreach ($this->fileDumperWriters as $dumpWriter) {
            /** @var \AppBundle\FileDumper\FileDumperWriter $dumpWriter */
            if (file_exists($dumpWriter->getFilePathName())) {
                unlink($dumpWriter->getFilePathName());
            }
            $this->output->writeln('Write dump for: '.$dumpWriter->getFilePathName());
            $dumpWriter->run();
        }
    }

    /**
     * Insert $stack into database
     * If option db-insert = new, we try to remove already existing media from $stack
     * If option db-insert = all, we insert all medias from $stack.
     *
     * @param $provider
     * @param $stack
     */
    private function insertDatabase($provider, $stack)
    {
        /** @var Registry $doctrine */
        $doctrine = $this->getContainer()->get('doctrine');
        $entityManager = $doctrine->getManager();
        $entityManager->getConnection()->getConfiguration()->setSQLLogger(null);

        if ($this->input->getOption('db-insert') == 'new') {
            $this->removeExistingMedia($stack);
        }

        foreach ($stack as $fileName) {
            $media = new Media();
            $media
                ->setProvider($provider)
                ->setFullPath($fileName);
            $entityManager->persist($media);
        }

        $entityManager->flush();
        $entityManager->clear();
    }

    /**
     * This method find all existing media from $stack.
     * Each media that already exist in database is removed in $stack.
     *
     * @param $stack
     */
    private function removeExistingMedia(&$stack)
    {
        /** @var Registry $doctrine */
        /* @var EntityRepository $mediaRepository */
        $doctrine = $this->getContainer()->get('doctrine');
        $mediaRepository = $doctrine->getRepository('\AppBundle\Entity\Media');
        $existing = $mediaRepository->findBy(['fullFilePathMd5' => array_map('md5', $stack)]);
        $founded = count($existing);

        if ($founded == count($stack)) {
            $stack = [];

            return;
        }

        if ($founded > 0) {
            $remove = [];
            foreach ($existing as $existingItem) {
                /* @var Media $existingItem */
                $remove[] = $existingItem->getFullPath();
            }
            $stack = array_diff($stack, $remove);
        }
    }

    /**
     * @param $client
     * @param $concurrency
     * @param $apiBase
     */
    protected function runAsyncUpdate($client, $concurrency, $apiBase, $max)
    {
        $requests = function ($concurrency, $apiBase, $max) {
            for ($j = 1; $j <= $max; ++$j) {
                $url = sprintf('%s/media?order[id]=desc&untaged=1&_trigger_update&_=%s', $apiBase, time());
                yield new Request('GET', $url);
            }
        };
        $pool = new Pool($client, $requests($concurrency, $apiBase, $max), [
            'fulfilled' => function ($response, $index) {
                $this->progressBar->advance(100);
            },
            'concurrency' => $concurrency,
        ]);
        $promise = $pool->promise();
        $promise->wait();
    }

    /**
     * Show configuration information.
     */
    private function info()
    {
        foreach ($this->fileDumperWriters as $fdw) {
            $this->output->writeln(sprintf("<info>FileDumperWriter: </info>\n\t- %s", $fdw->getName()));
            $this->output->writeln("<info>Paths:</info>");
            $this->output->writeln("\t- ".implode(PHP_EOL."\t- ", $fdw->getPaths()));
            $this->output->writeln("");
            $this->output->writeln(sprintf("<info>Output file: </info>\n\t- %s", $fdw->getFilePathName()));
        }

        exit(0);
    }

    /**
     * Insert dump files into database.
     */
    protected function produceMsg()
    {
        foreach ($this->getCollectionFileName() as $provider => $fp) {
            $reader = new FileDumperReader($fp);
            $ctn = $reader->count();
            $i = 0;
            $stack = [];
            $this->output->writeln(sprintf('Reading file \'<info>%s</info>\'', $fp));
            $this->output->writeln(sprintf('Try insert: <info>%s</info> media(s)', $ctn));
            $this->progressBar = new ProgressBar($this->output, $ctn);
            /** @var Producer $mediaIndexer */
            $mediaIndexer = $this->getContainer()->get('old_sound_rabbit_mq.sapar_media_indexer_producer');

            foreach ($reader as $line) {
                $stack[] = $line->getFile()->getPathname();
                ++$i;
                $this->progressBar->advance();
                $mediaIndexer->publish(json_encode(['pathName' => $line->getFile()->getPathname(), 'provider' => $provider]));
                gc_collect_cycles();
            }
            $this->progressBar->finish();
            $this->output->writeln('');
        }
    }

    /**
     * Update tags.
     * This command iterate on api calls from Media endpoint filtered by "Untaged".
     *
     */
    protected function updateTags()
    {
        $apiBase = $this->getContainer()->getParameter('api.base');
        $result = (json_decode(file_get_contents(sprintf('%s/media?order[id]=desc&untaged=1&_=%s', $apiBase, time())), true));
        if (intval($result['hydra:totalItems']) === 0) {
            return;
        }
        $pageCount = (ceil(intval($result['hydra:totalItems']) / intval($result['hydra:itemsPerPage'])));
        $concurrency = 15;
        if ($pageCount == 0) {
            return;
        }
        $maxIteration = ceil($pageCount / $concurrency);
        $client = new Client();
        $this->progressBar = new ProgressBar($this->output, $result['hydra:totalItems']);
        //for ($i = 1; $i <= $maxIteration; ++$i) {
        $this->runAsyncUpdate($client, $concurrency, $apiBase, $result['hydra:totalItems']);
        //$this->progressBar->advance(100*$concurrency);
        //}
        $this->progressBar->finish();
    }
}
