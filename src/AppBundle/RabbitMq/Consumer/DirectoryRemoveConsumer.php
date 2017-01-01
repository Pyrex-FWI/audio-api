<?php

namespace AppBundle\RabbitMq\Consumer;

use AppBundle\Event\DirectoryEvent;
use AppBundle\Service\TempDir;
use Doctrine\Bundle\DoctrineBundle\Registry;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DirectoryRemoveConsumer implements ConsumerInterface
{
    use ContainerAwareTrait;

    /**
     * @var LoggerInterface|NullLogger
     */
    private $logger;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    public function __construct(LoggerInterface $logger, EventDispatcherInterface $eventDispatcher)
    {
        $this->logger = $logger;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param AMQPMessage $msg The message
     *
     * @return mixed false to reject and requeue, any other value to acknowledge
     */
    public function execute(AMQPMessage $msg)
    {
        if (!is_dir($msg->body)) {
            return;
        }

        $fs = new \SplFileInfo($msg->body);
        $dirEvent = new DirectoryEvent($fs);

        /** @var TempDir $tmpDirManager */
        $tmpDirManager = $this->container->get('temp_dir');
        $tmpDirManager
            ->setTempDirectory($fs->getRealPath())
            ->setStrictMode(true);
        if ($tmpDirManager->readTempDirectoryMeta()) {
            $year = implode(' ,', $tmpDirManager->getTempDirectoryId3Years());
            $genres = implode(' ,', $tmpDirManager->getTempDirectoryId3Genres());
            $artists = implode(' ,', $tmpDirManager->getTempDirectoryId3Artists());
            $albums = implode(' ,', $tmpDirManager->getTempDirectoryId3Albums());
            $dirEvent = new DirectoryEvent($fs, $genres, $albums, $artists, $year);
        }
        $command = 'rm -rf '.escapeshellarg($fs->getRealPath());
        exec($command, $out, $returnVar);
        echo sprintf('%s - %s: %s ', date('d/m/y H:i:s'), $command, boolval(!$returnVar))."\n";
        if (!$returnVar) {
            $this->logger->info(sprintf('%s has been removed correctly', $msg->body));
        }
        /** @var Registry $doctrine */
        $doctrine = $this->container->get('doctrine');
        $doctrine->resetManager();
        try {
            $this->eventDispatcher->dispatch(\AppBundle\Event\Event::DIRECTORY_POST_DELETE, $dirEvent);
        } catch (\Exception $e) {
            echo sprintf('%s - %s: %s ', date('d/m/y H:i:s'), $e->getMessage())."\n";

            return false;
        }
        $doctrine->getConnection()->close();
        gc_collect_cycles();
    }
}
