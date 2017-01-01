<?php

namespace Pyrex\RadioBundle\Listener;

use Pyrex\CoreModelBundle\Entity\RadioHit;
use Doctrine\Bundle\DoctrineBundle\Registry;
use HitsBundle\Event\HitsBundleEvent;
use HitsBundle\Event\SourceItemEvent;
use Monolog\Logger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Serializer\Serializer;

/**
 * RadioEventSubscriber.
 */
class RadioEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var Registry
     */
    private $doctrine;
    private $serializer;
    private $logger;

    public function __construct(Registry $doctrine, Serializer $serializer, Logger $logger)
    {
        $this->doctrine = $doctrine;
        $this->serializer = $serializer;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            HitsBundleEvent::SOURCE_ITEM => ['OnItemReceived'],
        );
    }

    public function OnItemReceived(SourceItemEvent $itemEvent)
    {
        $this->logger->info($itemEvent->getItem()->getArtist());
        $manager = $this->doctrine->getManager();
        $hit = new RadioHit();
        $hit->setArtist($itemEvent->getItem()->getArtist());
        $hit->setTitle($itemEvent->getItem()->getTitle());
        //dump($manager->getClassMetadata('Pyrex\CoreModelBundle\Entity\RadioHit'));
        $similarHit = $manager->getRepository('\Pyrex\CoreModelBundle\Entity\RadioHit')->getSimilar($hit->getArtist(), $hit->getTitle(), 75);
//
//        if ($similarHit) {
//            dump('Similar for '. $hit->getArtist().' '.$hit->getTitle());
//            dump($similarHit);
//        }
//        $this->doctrine->getManager()->persist($hit);
//        $this->doctrine->getManager()->flush();
    }
}
