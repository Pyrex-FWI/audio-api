<?php

namespace AppBundle\Tests\EventListener;

use AppBundle\Entity\Media;
use AppBundle\Event\DirectoryEvent;
use DeejayPoolBundle\Entity\AvdItem;
use DeejayPoolBundle\Entity\FranchisePoolItem;
use DeejayPoolBundle\Entity\SvItem;
use DeejayPoolBundle\Event\ItemDownloadEvent;
use DeejayPoolBundle\Event\PostItemsListEvent;
use DeejayPoolBundle\Event\ProviderEvents;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Faker\Generator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * AppEventSubscriber.
 */
class AppEventSubscriberTest extends KernelTestCase
{
    private static function createItem($provider)
    {
        $faker = static::$kernel->getContainer()->get('faker.generator');
        if ($provider === Media::PROVIDER_AV_DISTRICT) {
            $item = new AvdItem();
            $item->setItemId($faker->id);
            $item->setArtist($faker->artist)
                ->setTitle($faker->title)
                ->setBpm($faker->bpm)
                ->addRelatedGenre($faker->genre)
                ->addRelatedGenre($faker->genre)
                ->addRelatedGenre($faker->genre)
                ->setDownloadlink($faker->url)
                ->setFullPath(static::$kernel->getContainer()->getParameter('av_district.configuration.root_path').'/'.uniqid('file_'))
                ->setVersion($faker->version)
                ->setReleaseDate($faker->dateTime);
        }

        if ($provider === Media::PROVIDER_FRP_AUDIO  || $provider === Media::PROVIDER_FRP_VIDEO) {
            $item = new FranchisePoolItem();
            $item->setItemId($faker->id);
            $item->setArtist($faker->artist)
                ->setTitle($faker->title)
                ->addRelatedGenre($faker->genre)
                ->setDownloadlink($faker->url)
                ->setFullPath(static::$kernel->getContainer()->getParameter('av_district.configuration.root_path').'/'.uniqid('file_'))
                ->setVersion($faker->version)
                ->setAudio(true)
                ->setReleaseDate($faker->dateTime);
            if ($provider === Media::PROVIDER_FRP_VIDEO) {
                $item->setAudio(false);
                $item->setVideo(true);
            }
        }
        if ($provider === Media::PROVIDER_SMASHVISION) {
            $item = new SvItem();
            $item->setItemId($faker->id);
            $item->setArtist($faker->artist)
                ->setTitle($faker->title)
                ->setBpm($faker->bpm)
                ->addRelatedGenre($faker->genre)
                ->addRelatedGenre($faker->genre)
                ->addRelatedGenre($faker->genre)
                ->setDownloadlink($faker->url)
                ->setFullPath(static::$kernel->getContainer()->getParameter('av_district.configuration.root_path').'/'.uniqid('file_'))
                ->setVersion($faker->version)
                ->setReleaseDate($faker->dateTime);
        }

        return $item;
    }

    public function setUp()
    {
        parent::setUpBeforeClass();
        static::bootKernel([]);
    }

    public function testAllProviders()
    {
        /** @var EventDispatcher $evtDispatch */
        $evtDispatch = static::$kernel->getContainer()->get('event_dispatcher');
        /** @var  Registry $doctrine */
        $doctrine  = static::$kernel->getContainer()->get('doctrine');
        $providers = Media::getProviders();
        foreach ($providers as $provider) {
            if ($provider == Media::PROVIDER_DIGITAL_DJ_POOL) {
                continue;
            }

            for ($i = 0; $i < 20; ++$i) {
                $item  = self::createItem($provider);
                $exist = $doctrine->getRepository(Media::getProviderEntityClass($provider))->findOneBy(['providerId' => $item->getItemId()]);
                if ($exist) {
                    $doctrine->getManager()->remove($exist);
                    $doctrine->getManager()->flush();
                }

                $event = new ItemDownloadEvent($item);
                $evtDispatch->dispatch(ProviderEvents::ITEM_SUCCESS_DOWNLOAD, $event);
                $after = $doctrine->getRepository(Media::getProviderEntityClass($provider))->findBy(['providerId' => $item->getItemId()]);
                $this->assertEquals(1, count($after));
                break;
            }
        }
    }

    public function testDuplicate()
    {
        /** @var EventDispatcher $evtDispatch */
        $evtDispatch = static::$kernel->getContainer()->get('event_dispatcher');
        /** @var  Registry $doctrine */
        $doctrine = static::$kernel->getContainer()->get('doctrine');

        $items = [];
        for ($i = 0; $i < 20; ++$i) {
            $items[] = self::createItem(Media::PROVIDER_AV_DISTRICT);
        }

        $this->assertCount(20, $items);
        $event = new ItemDownloadEvent($items[0]);
        $evtDispatch->dispatch(ProviderEvents::ITEM_SUCCESS_DOWNLOAD, $event);

        $eventList = new PostItemsListEvent($items);
        $evtDispatch->dispatch(ProviderEvents::ITEMS_POST_GETLIST, $eventList);

        $this->assertEquals(count($eventList->getItems()) + 1, count($items));
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            ProviderEvents::ITEM_SUCCESS_DOWNLOAD => ['OnAvdSuccessDownload', 64],
        );
    }

    /**
     * @param ItemDownloadEvent $event
     */
    public function OnAvdSuccessDownload(ItemDownloadEvent $event)
    {
        $avdItem = $event->getItem();
    }

    public static function createFrpAudioItem()
    {
        /** @var Generator $faker */
        $faker    = static::$kernel->getContainer()->get('faker');
        $frpAudio = new FranchisePoolItem();
        $frpAudio->setItemId($faker->id);
        $frpAudio->setArtist($faker->artist)
            ->setTitle($faker->title)
            ->addRelatedGenre($faker->genre)
            ->setDownloadlink($faker->url)
            ->setFullPath(static::$kernel->getContainer()->getParameter('av_district.configuration.root_path').'/file')
            ->setVersion($faker->version)
            ->setAudio(true)
            ->setReleaseDate($faker->dateTime);

        return $frpAudio;
    }

    public static function createAvItem()
    {
        /** @var Generator $faker */
        $faker = static::$kernel->getContainer()->get('faker');
        $avd   = new AvdItem();
        $avd->setItemId($faker->id);
        $avd->setArtist($faker->artist)
            ->setTitle($faker->title)
            ->setBpm($faker->bpm)
            ->addRelatedGenre($faker->genre)
            ->addRelatedGenre($faker->genre)
            ->addRelatedGenre($faker->genre)
            ->setDownloadlink($faker->url)
            ->setFullPath(static::$kernel->getContainer()->getParameter('av_district.configuration.root_path').'/file')
            ->setVersion($faker->version)
            ->setReleaseDate($faker->dateTime);

        return $avd;
    }

    public function testDirectoryPostDelete()
    {
        /** @var EventDispatcher $evtDispatch */
        $evtDispatch = static::$kernel->getContainer()->get('event_dispatcher');
        /** @var  Registry $doctrine */
        $doctrine   = static::$kernel->getContainer()->get('doctrine');
        $dirEvent   = new DirectoryEvent(new \SplFileInfo('/volume3/temp/Jus_Shane-Stronger-WEB-2015-JAH'), 'Dance Hall', 'Some album', 'Some artist', 2011);
        $getDeleted = function () use ($doctrine, $dirEvent) {
            return $doctrine->getRepository('\Pyrex\CoreModelBundle\Entity\DeletedRelease')->findByRawName($dirEvent->getDirName());
        };
        $doctrine->getManager()->createQuery('DELETE FROM \Pyrex\CoreModelBundle\Entity\DeletedRelease')->execute();
        $this->assertEquals(0, count($getDeleted()));
        $evtDispatch->dispatch(\AppBundle\Event\Event::DIRECTORY_POST_DELETE, $dirEvent);
        $this->assertEquals(1, count($getDeleted()));
        $evtDispatch->dispatch(\AppBundle\Event\Event::DIRECTORY_POST_DELETE, $dirEvent);
        $this->assertEquals(1, count($getDeleted()));
    }
}
