<?php

namespace AppBundle\EventListener;

use AudioCoreEntity\EntityArtist;
use AudioCoreEntity\Entity\Genre;
use AppBundle\Entity\Media;
use AppBundle\Service\GenreStack;
use Deejay\Id3ToolBundle\Wrapper\Mediainfo;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Dunglas\ApiBundle\Doctrine\Orm\Paginator;
use Dunglas\ApiBundle\Event\DataEvent;
use Dunglas\ApiBundle\Event\Events;
use Monolog\Logger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\VarDumper\VarDumper;

/**
 * ApiEventSubscriber.
 */
class ApiEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var Registry
     */
    private $doctrine;
    /** @var  Logger */
    private $logger;
    /** @var RequestStack  */
    private $request_stack;
    /** @var  Mediainfo */
    private $mediainfo;
    /** @var  EntityManager */
    private $eManager;
    /** @var  EntityRepository */
    private $genreRepo;
    /** @var  EntityRepository */
    private $mediaRepo;
    /** @var \Doctrine\Common\Persistence\ObjectRepository  */
    private $artistRepo;
    /** @var  GenreStack */
    private $genreStack;

    public function __construct(Registry $doctrine, RequestStack $request_stack, Mediainfo $mediaInfo, Logger $logger)
    {
        $this->doctrine = $doctrine;
        $this->eManager = $doctrine->getManager();
        $this->logger = $logger;
        $this->request_stack = $request_stack;
        $this->mediainfo = $mediaInfo;
        $this->genreRepo = $this->eManager->getRepository(\AudioCoreEntity\Entity\Genre::class);
        $this->mediaRepo = $this->eManager->getRepository(\AppBundle\Entity\Media::class);
        $this->artistRepo = $this->eManager->getRepository(\AudioCoreEntity\Entity\Artist::class);
    }

    /**
     * @param GenreStack $genreStack
     */
    public function setGenreStack(GenreStack $genreStack)
    {
        $this->genreStack = $genreStack;
    }
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            Events::RETRIEVE_LIST => ['tagList', 64],
        );
    }

    /**
     * This Method Update tag on untagged Media.
     *
     * @param DataEvent $event
     */
    public function tagList(DataEvent $event)
    {
        if ('AppBundle\Entity\Media' !== $event->getResource()->getEntityClass()) {
            return;
        }

        $queryString = [];
        parse_str($this->request_stack->getMasterRequest()->getQueryString(), $queryString);

        if (!isset($queryString['_trigger_update'])) {
            return;
        }

        /** @var Paginator $items */
        $items = $event->getData();
        $entityManager = $this->doctrine->getManager();
        $artistRepo = $entityManager->getRepository('AppBundle\Entity\Artist');

        foreach ($items->getIterator() as $media) {
            /** @var Media $media */
            $this->mediainfo->read($media->getFullPath());
            if ($media->isUntaged()) {
                $media
                    ->setTitle($this->mediainfo->getTitle())
                    ->setArtist($this->mediainfo->getArtist())
                    ->setBpm($this->mediainfo->getBpm())
                    ->setYear($this->mediainfo->getYear())
                ;

                if ($this->mediainfo->getGenres()) {
                    $genres = (array) $this->mediainfo->getGenres();
                    foreach ($genres as $genre) {
                        if (!$genre) {
                            continue;
                        }

                        $existGenre = $this->genreRepo->findOneBy(['name' => $genre]);
                        if ($existGenre) {
                            $media->addGenre($existGenre);
                            continue;
                        }
                        $genre = new Genre($genre);
                        $entityManager->persist($genre);
                        $entityManager->flush();
                        $media->addGenre($genre);
                    }
                }
                if ($artists = $this->mediainfo->getArtists()) {
                    foreach ($artists as $artist) {
                        if (!$artist) {
                            continue;
                        }
                        $existArtist = $artistRepo->findOneBy(['name' => $artist]);
                        if ($existArtist) {
                            $media->addArtist($existArtist);
                            continue;
                        }
                        $artist = new Artist($artist);

                        $entityManager->persist($artist);
                        $entityManager->flush();
                        $media->addArtist($artist);
                    }
                }
                $media->setTagged(true);
                $entityManager->persist($media);
            }
        }

        $entityManager->flush();
    }

    /**
     * @param $genres
     * @return array
     */
    private function getOrCreateGenres($genres)
    {
        $stack = [];
        foreach ($genres as $genreName) {
            if (!$genreName) {
                continue;
            }
            $stack[] = $this->genreStack->getOrCreateIfNotExist($genreName);
        }

        return $stack;
    }

    /**
     * @param $artists
     * @return array
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Exception
     */
    private function getOrCreateArtists($artists)
    {
        $stack = [];
        foreach ($artists as $artist) {
            if (!$artist) {
                continue;
            }
            $existArtist = $this->artistRepo->findOneBy(['name' => $artist]);
            if ($existArtist) {
                $stack[] = $existArtist;
                continue;
            }

            $this->eManager->getConnection()->beginTransaction();
            $artist = new Artist($artist);
            try {
                $this->eManager->persist($artist);
                $this->eManager->flush();
                $this->eManager->getConnection()->commit();
                $stack[] = $artist;
            } catch (\Exception $e) {
                $this->eManager->getConnection()->rollback();
                throw $e;
            }
        }

        return $stack;
    }

    public function _tagList(DataEvent $event)
    {
        if ('AppBundle\Entity\Media' !== $event->getResource()->getEntityClass()) {
            return;
        }

        $queryString = [];
        parse_str($this->request_stack->getMasterRequest()->getQueryString(), $queryString);

        if (!isset($queryString['_trigger_update'])) {
            return;
        }

        /** @var Paginator $items */
        $items = $event->getData();
        /** @var Media[] $mediaFiles */
        $mediaFiles = [];
        foreach ($items->getIterator() as $media) {
            if (!$media->isUntaged()) continue;
            /* @var Media $media */
            $mediaFiles[$media->getFullPath()] = $media;
        }
        $mediaTags = $this->mediainfo->readMultiple(array_keys($mediaFiles));
        $mediaCount = count($mediaFiles);
        $tagsCount = count($mediaTags);
        for ($i = 0; $i < $tagsCount; ++$i) {
            $tag = $this->mediainfo->eq($i);
            $genres = $this->getOrCreateGenres($tag->getGenres());
            $artists =$this->getOrCreateArtists($tag->getArtists());
            $mediaFiles[$tag->getFileName()]
                ->setTitle($tag->getTitle())
                ->setArtist($tag->getArtist())
                ->setBpm($tag->getBpm())
                ->setYear($tag->getYear())
                //->setGenres($this->getOrCreateGenres($tag->getGenres()))
                //->setArtists($this->getOrCreateArtists($this->mediainfo->getArtists()))
                ->setTagged(true)
            ;
            foreach ($genres as $genre) {
                $mediaFiles[$tag->getFileName()]->addGenre($genre);
            }
            foreach ($artists as $artist) {
                $mediaFiles[$tag->getFileName()]->addArtist($artist);
            }
            $this->eManager->persist($mediaFiles[$tag->getFileName()]);
        }
        $this->eManager->flush();
    }
}
