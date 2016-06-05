<?php

namespace AppBundle\EventListener;

use AppBundle\Converter\ItemConverter;
use AppBundle\Entity\Media;
use AppBundle\Event\DirectoryEvent;
use AppBundle\Event\Event;
use AudioCoreEntity\Entity\DeletedRelease;
use DeejayPoolBundle\Event\ItemLocalExistenceEvent;
use DeejayPoolBundle\Event\ItemDownloadEvent;
use DeejayPoolBundle\Event\PostItemsListEvent;
use DeejayPoolBundle\Event\ProviderEvents;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityRepository;
use Monolog\Logger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Serializer\Serializer;

/**
 * AppEventSubscriber.
 */
class AppEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var Registry
     */
    private $doctrine;
    private $serializer;
    private $logger;
    private $config;

    public function __construct(Registry $doctrine, Serializer $serializer, Logger $logger, $config = [])
    {
        $this->doctrine = $doctrine;
        $this->serializer = $serializer;
        $this->logger = $logger;
        $this->config = array_merge(
            ['database_check'   => false],
            $config
        );
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            ProviderEvents::ITEM_SUCCESS_DOWNLOAD   => ['OnSuccessDownload', 64],
            ProviderEvents::ITEMS_POST_GETLIST      => ['removeAlreadyDownloaded', 120],
            ProviderEvents::SEARCH_ITEM_LOCALY      => ['findItemLocaly', 64],
            Event::DIRECTORY_POST_MOVE              => ['directoryPostMove', 64],
            Event::DIRECTORY_POST_DELETE            => ['directoryPostDelete', 64],
        );
    }

    public function findItemLocaly(ItemLocalExistenceEvent $itemEvent)
    {
        $manager = $this->doctrine->getManager();
        $provider = Media::getProviderFromItem($itemEvent->getItem());
        /** @var EntityRepository $mediaRepo */
        $mediaRepo = $manager->getRepository(Media::getProviderEntityClass($provider));
        $localItem = $mediaRepo->findOneBy(
            [
                'providerId'    => $itemEvent->getItem()->getItemId()
            ]
        );

        if ($localItem) {
            $itemEvent->setExistLocaly(true);
        }
    }

    /**
     * This function remove all items was already downloaded
     * @param PostItemsListEvent $postList
     */
    public function removeAlreadyDownloaded(PostItemsListEvent $postList)
    {
        if (count($postList->getItems()) === 0) {
            return;
        }

        if (true === $this->config['database_check']) {
            return;
        }

        $mustDownload = [];
        $provider = null;
        $itemIds = [];
        $existIds = [];

        foreach ($postList->getItems() as $sampleItem) {
            $provider = Media::getProviderFromItem($sampleItem);
            $itemIds[] = $sampleItem->getItemId();
        }

        if ($provider) {
            $exists = $this->doctrine->getRepository(Media::getProviderEntityClass($provider))->findBy(['providerId' => $itemIds]);
            foreach ($exists as $exist) {
                /* @var Media $exist */
                $existIds[] = $exist->getProviderId();
            }
            foreach ($postList->getItems() as $sampleItem) {
                if (in_array($sampleItem->getItemId(), $existIds)) {
                    $this->logger->info(
                        sprintf(
                            'SKIPED because %s %s - %s is already downloaded',
                            $sampleItem->getItemId(),
                            $sampleItem->getArtist(),
                            $sampleItem->getTitle()
                        )
                    );
                    continue;
                }
                $mustDownload[] = $sampleItem;
            }
        }
        $postList->setItems($mustDownload);
    }

    /**
     * @param ItemDownloadEvent $event
     *
     * @throws \Exception
     */
    public function OnSuccessDownload(ItemDownloadEvent $event)
    {
        if (true === $this->config['database_check']) {
            return;
        }

        $avdItem = $event->getItem();
        $manager = $this->doctrine->getManager();
        $genreRepo = $manager->getRepository(\AudioCoreEntity\Entity\Genre::class);

        $provider = null;
        $type = null;
        if ($avdItem instanceof \DeejayPoolBundle\Entity\AvdItem) {
            $provider = Media::PROVIDER_AV_DISTRICT;
        } elseif ($avdItem instanceof \DeejayPoolBundle\Entity\FranchisePoolItem) {
            if ($avdItem->isAudio()) {
                $provider = Media::PROVIDER_FRP_AUDIO;
            } else {
                $provider = Media::PROVIDER_FRP_VIDEO;
            }
        } elseif ($avdItem  instanceof \DeejayPoolBundle\Entity\SvItem) {
            $provider = Media::PROVIDER_SMASHVISION;
        } else {
            throw new \Exception('Undetermined PROVIDER');
        }
        /** @var EntityRepository $mediaRepo */
        $mediaRepo = $manager->getRepository(Media::getProviderEntityClass($provider));
        $mediaItem = $mediaRepo->findOneBy(['providerId' => $avdItem->getItemId()]);
        if (!$mediaItem) {
            $media = (new ItemConverter())->getMediaFromProviderItem($avdItem);
            $genres = $media->getGenres();
            foreach ($genres as $genre) {
                $existGenre = $genreRepo->findOneBy(['name' => $genre->getName()]);
                if ($existGenre && $media->removeGenre($genre)) {
                    $media->addGenre($existGenre);
                }
            }
            $manager->persist($media);
            $manager->flush();
        }
    }

    public function directoryPostMove(DirectoryEvent $directoryEvent)
    {
        //dump($directoryEvent->getArtist());
        //dump($directoryEvent->getAlbumName());
        //dump($directoryEvent->getDirName());
    }

    public function directoryPostDelete(DirectoryEvent $directoryEvent)
    {
        $manager = $this->doctrine->getManager();
        $deletedDirRepo = $manager->getRepository('\AudioCoreEntity\Entity\DeletedRelease');

        if (!$deletedDirRepo->findOneByRawName($directoryEvent->getDirName())) {

            $deletedDir = new DeletedRelease();
            $deletedDir
                ->setArtistName($directoryEvent->getArtist())
                ->setGenre($directoryEvent->getGenreName())
                ->setAlbumName($directoryEvent->getAlbumName())
                ->setDeletedDate(new \DateTime())
                ->setRawName($directoryEvent->getDirName());
            $manager->persist($deletedDir);
            $manager->flush();
        }
    }
}
