<?php
namespace AppBundle\Tests\Async;

use AppBundle\Entity\Media;
use Faker\Generator;
use OldSound\RabbitMqBundle\Command\PurgeConsumerCommand;
use OldSound\RabbitMqBundle\RabbitMq\Consumer;
use OldSound\RabbitMqBundle\RabbitMq\Producer;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use AppBundle\FileDumper\FileDumperWriter;
use org\bovigo\vfs\vfsStream;
use AppBundle\FileDumper\FileDumperReader;
use AppBundle\FileDumper\FileDumperRow;
/**
 * WorkflowTest.
 */
class WorkflowTest extends KernelTestCase
{
    /** @var  Container */
    private $container;
    /** @var FileDumperWriter */
    private $fileDumper;
    /** @var  Application */
    private $application;
    private $testconf;

    protected function setUp()
    {
        static::bootKernel();
        $this->container    = static::$kernel->getContainer();
        $this->testConf = $this->container->getParameter('collection.paths')['test'];
        $this->application = new Application(self::$kernel);
        $this->application->add(new PurgeConsumerCommand());
    }

    /**
     * @test
     */
    public function start()
    {
        /** @var Consumer $mediaIndexerConsumer */
        $mediaIndexerConsumer = $this->container->get('old_sound_rabbit_mq.media_create_media_reference_consumer');
        $mediaIndexerConsumer->purge();
        /** @var Consumer $mediaIndexerConsumer */
        $mediaIndexerConsumer = $this->container->get('old_sound_rabbit_mq.media_read_tag_consumer');
        $mediaIndexerConsumer->purge();

        $mediaIndexerConsumer = $this->container->get('old_sound_rabbit_mq.media_update_tag_consumer');
        $mediaIndexerConsumer->purge();

    }
    /**
     * @test
     * @depends start
     * @return
     */
    public function produceFileIndex()
    {

        $fileData   = \AppBundle\Tests\FileDumper\FileDumperTest::writeTestDumpFile('/tmp/workflow_test.txt', $this->testConf['paths'][0], $this->testConf['provider'], 400);
        /** @var Producer $mediaIndexer */
        $mediaIndexer = $this->container->get('old_sound_rabbit_mq.media_create_media_reference_producer');
        $mediaIndexer->setContentType('application/json');
        $reader     = new FileDumperReader('/tmp/workflow_test.txt');
        foreach ($reader as $line) {
            $producerData = [
                'pathName'  => $line->getFile()->getPathname(),
                'provider'  => $line->getProvider(),
                'mediaRef'  => null,
            ];
            $stack[] = $line->getFile()->getPathname();
            $mediaIndexer->publish($this->container->get('serializer')->serialize($producerData, 'json'));
            gc_collect_cycles();
        }
    }

    /**
     * @test
     * @depends produceFileIndex
     * @return
     */
    public function consumerFileIndex()
    {
        $mediaIndexerConsumer = $this->container->get('old_sound_rabbit_mq.media_create_media_reference_consumer');
        $mediaIndexerConsumer->consume(400);
    }
}
