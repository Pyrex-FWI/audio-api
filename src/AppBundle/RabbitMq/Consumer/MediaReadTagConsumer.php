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
 * Class MediaReaTagConsumer
 * @package AppBundle\RabbitMq\Consumer
 */
class MediaReadTagConsumer implements ConsumerInterface
{
    use ContainerAwareTrait;
    private static $count = 0;

    /**
     * @var LoggerInterface|NullLogger
     */
    private $logger;

    /** @var  EventDispatcherInterface */
    private $eventDispatcher;

    /**
     * MediaIndexerConsumer constructor.
     * @param LoggerInterface          $logger
     * @param EventDispatcherInterface $eventDispatcher
     */
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
        self::$count++;

        $data = $this->container->get('serializer')->decode($msg->body, 'json');
        $this->logger->info(self::$count.' - '.$data['pathName']);


        if (!file_exists($data['pathName'])) {
            $this->logger->info(sprintf('File %s not exist'), $data['file']);

            return true;
        }

        $mediaTagReader = $this->container->get('app.media_tag_reader');
        try {
            if ($id3Metadata = $mediaTagReader->read($data['pathName'])) {
                $this->logger->info('MediaIndexer success for '.$data['pathName']);
                $data['id3Metadata'] = $id3Metadata;
                $producerData = $this->container->get('serializer')->serialize($data, 'json');
                /** @var Producer $producer */
                $producer = $this->container->get('old_sound_rabbit_mq.media_update_tag_producer');
                $producer->setContentType('application/json');
                $producer->publish($producerData);
            }
        } catch (UnexpectedValueException $e) {
            $this->logger->error(sprintf('Serialization error for %s.', $data['pathName']), [$data]);

        } catch (\Exception $e) {
            $this->logger->error(sprintf('Unknow error for %s. %s', $data['pathName'], $e->getMessage()), [$data]);

        }
        gc_collect_cycles();

        return true;
    }
}
