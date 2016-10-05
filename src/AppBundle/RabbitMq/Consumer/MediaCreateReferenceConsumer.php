<?php

namespace AppBundle\RabbitMq\Consumer;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use OldSound\RabbitMqBundle\RabbitMq\Producer;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;

/**
 * Class MediaCreateReferenceConsumer
 * @package AppBundle\RabbitMq\Consumer
 */
class MediaCreateReferenceConsumer implements ConsumerInterface
{
    use ContainerAwareTrait;
    private static $count = 0;

    /**
     * @var LoggerInterface|NullLogger
     */
    private $logger;

    /**
     * MediaIndexerConsumer constructor.
     * @param LoggerInterface          $logger
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger          = $logger;
    }

    /**
     * @param AMQPMessage $msg The message
     *
     * @return mixed false to reject and requeue, any other value to acknowledge
     */
    public function execute(AMQPMessage $msg)
    {
        self::$count++;

        $data = $this->container->get('serializer')->decode($msg->body, 'json');
        $this->logger->info(self::$count.' - '.$data['pathName']);


        if (!file_exists($data['pathName'])) {
            $this->logger->info(sprintf('File %s not exist'), $data['pathName']);

            return true;
        }

        $media = $this->container->get('repository.media')->createIfNotExist($data['pathName'], $data['provider']);
        $data['mediaRef'] = $media->getId();
        $producerData = $this->container->get('serializer')->serialize($data, 'json');
        /** @var Producer $producer */
        $producer = $this->container->get('old_sound_rabbit_mq.media_read_tag_producer');
        $producer->setContentType('application/json');
        $producer->publish($producerData);

        return true;
    }
}
