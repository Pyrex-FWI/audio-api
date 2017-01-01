<?php

namespace AppBundle\RabbitMq\Consumer;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;
use Sapar\Id3\Metadata\Id3Metadata;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class MediaUpdateTagConsumer.
 */
class MediaUpdateTagConsumer implements ConsumerInterface
{
    use ContainerAwareTrait;
    private static $count = 0;

    /**
     * @var LoggerInterface|NullLogger
     */
    private $logger;

    /**
     * MediaIndexerConsumer constructor.
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param AMQPMessage $msg The message
     *
     * @return mixed false to reject and requeue, any other value to acknowledge
     */
    public function execute(AMQPMessage $msg)
    {
        ++self::$count;

        $serializer = $this->container->get('serializer');
        $data = $serializer->decode($msg->body, 'json');
        $this->logger->info(self::$count.' - '.$data['pathName']);

        /** @var Id3Metadata $id3Metadata */
        $id3Metadata = $this->container->get('serializer')->denormalize(
            $data['id3Metadata'],
            Id3Metadata::class,
            'json',
            ['file_path' => $data['pathName']]
        );

        $mediaTagUpdate = $this->container->get('app.media_tag_update');
        $mediaTagUpdate->update($id3Metadata, $data['provider'], @$data['mediaRef']);

        return true;
    }
}
