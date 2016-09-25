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
     * this method is called by DI or CollectionDumperPass.
     *
     * @param FileDumperWriter $fileDumperWriter
     */
    public function addFileDumperWriter(FileDumperWriter $fileDumperWriter)
    {
        $this->fileDumperWriters[$fileDumperWriter->getName()] = $fileDumperWriter;
    }
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('media:dump:collection')->setDescription('Dump|Import|Update collection files into databases')
            ->addOption(
                'dump',
                null,
                InputOption::VALUE_NONE,
                'This option dump all dirs registered into %collection.dirs% parameter',
                null
            )
            ->addOption(
                'info',
                null,
                InputOption::VALUE_NONE,
                'Print all availablse FileDumpWriters',
                null
            )
            ->addOption(
                'async-insert',
                null,
                InputOption::VALUE_NONE,
                'Send message to rabbit mq for database insertion',
                null
            )
            ->setHelp(
                <<<EOT
Dump your collection via %ddp.console.collection_dumper.shell_command%
EOT
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        ini_set('memory_limit', '-1');
        gc_enable();
        $this->input  = $input;
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
            $this->output->writeln('<info>Paths:</info>');
            $this->output->writeln("\t- ".implode(PHP_EOL."\t- ", $fdw->getPaths()));
            $this->output->writeln('');
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
            $ctn    = $reader->count();
            $i      = 0;
            $stack  = [];
            $this->output->writeln(sprintf('Reading file \'<info>%s</info>\'', $fp));
            $this->output->writeln(sprintf('Try insert: <info>%s</info> media(s)', $ctn));
            $this->progressBar = new ProgressBar($this->output, $ctn);
            /** @var Producer $mediaIndexer */
            $mediaIndexer = $this->getContainer()->get('old_sound_rabbit_mq.media_read_tag_producer');
            $mediaIndexer->setContentType('application/json');
            foreach ($reader as $line) {
                $producerData = [
                    'pathName'  => $line->getFile()->getPathname(),
                    'provider'  => $provider,
                    'mediaRef'  => null,
                ];

                $stack[] = $line->getFile()->getPathname();
                ++$i;
                $this->progressBar->advance();

                if ($this->getContainer()->getParameter('app.library.indexing.workflow.create_media_reference_before_read_tag')) {
                    $media = $this->getContainer()->get('app.media_create_reference')->createReferenceIfNotExist(
                        $line->getFile()->getPathname(),
                        $provider
                    );
                    $producerData['mediaRef'] = $media->getId();
                }

                $mediaIndexer->publish($this->getContainer()->get('serializer')->serialize($producerData, 'json'));
                gc_collect_cycles();
            }
            $this->progressBar->finish();
            $this->output->writeln('');
        }
    }
}
