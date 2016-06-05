<?php

namespace AppBundle\RabbitMq\Consumer;

use AppBundle\Entity\Media;
use Cpyree\Id3\Metadata\Id3Metadata;
use Doctrine\Bundle\DoctrineBundle\Registry;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class MediaIndexerConsumer implements ConsumerInterface
{
    use ContainerAwareTrait;

    /**
     * @var LoggerInterface|NullLogger
     */
    private $logger;

    /** @var  EventDispatcherInterface */
    private $eventDispatcher;
    public function __construct(LoggerInterface $logger, EventDispatcherInterface $eventDispatcher)
    {
        $this->logger          = $logger;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param AMQPMessage $msg The message
     *
     * @return mixed false to reject and requeue, any other value to acknowledge
     */
    public function execute(AMQPMessage $msg)
    {
        $data = json_decode($msg->body, true);
        print_r($msg->body);
        echo "\n\n";
        /* @var Media $media */
        $mediaClass = Media::getProviderClass($data['provider']);
        $media      = new $mediaClass();

        /** @var Registry $doctrine */
        $doctrine = $this->container->get('doctrine');
        $doctrine->resetManager();
        try {
            /** @var \AppBundle\Id3\Id3Manager $id3Tool */
            $id3Tool = $this->container->get('id3_manager');
            $id3Meta = new Id3Metadata($data['pathName']);
            $id3Tool->read($id3Meta);
            var_dump($id3Meta);
            //$media->setFullPath($data['pathName']);
            //$doctrine->getManager()->persist($media);
            //$doctrine->getManager()->flush();
        } catch (\Exception $e) {
            echo sprintf('%s: %s ', date('d/m/y H:i:s'), $e->getMessage())."\n";

            //return false;
        }
        $doctrine->getConnection()->close();
        gc_collect_cycles();
    }
}
