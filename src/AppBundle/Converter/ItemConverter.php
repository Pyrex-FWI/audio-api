<?php

namespace AppBundle\Converter;

use AppBundle\Entity\Provider\AvDistrictMedia;
use AppBundle\Entity\Provider\DigitalDjPoolMedia;
use AppBundle\Entity\Provider\FranchiseAudioMedia;
use AppBundle\Entity\Provider\FranchiseVideoMedia;
use AppBundle\Entity\Provider\SmashVidzMedia;
use AudioCoreEntity\Entity\Genre;
use AppBundle\Entity\Media;
use DeejayPoolBundle\Entity\FranchisePoolItem;
use DeejayPoolBundle\Entity\ProviderItemInterface;

/**
 * @author Pyrex-FWI <yemistikris@hotmail.fr>
 *
 * ItemConverter
 */
class ItemConverter
{
    public function getMediaFromProviderItem(ProviderItemInterface $item)
    {
        $provider = null;
        $type     = null;
        if ($item instanceof \DeejayPoolBundle\Entity\AvdItem) {
            $media = new AvDistrictMedia();
            $type  = Media::MEDIA_TYPE_VIDEO;
        } elseif ($item instanceof FranchisePoolItem) {
            if ($item->isAudio()) {
                $media = new FranchiseAudioMedia();
                $type  = Media::MEDIA_TYPE_AUDIO;
            } else {
                $media = new FranchiseVideoMedia();
                $type  = Media::MEDIA_TYPE_VIDEO;
            }
        } elseif ($item instanceof \DeejayPoolBundle\Entity\SvItem) {
            $media = new SmashVidzMedia();
            $type  = Media::MEDIA_TYPE_AUDIO;
        } elseif ($item instanceof \DeejayPoolBundle\Entity\DdpItem) {
            $media = new DigitalDjPoolMedia;
            $type  = Media::MEDIA_TYPE_AUDIO;
        } else {
            throw new \Exception('Undetermined PROVIDER');
        }

        $media
            ->setArtist($item->getArtist())
            ->setBpm($item->getBpm())
            ->setFullPath($item->getFullPath())
            //->setProvider($provider)
            ->setProviderId($item->getItemId())
            ->setProviderUrl($item->getDownloadlink())
            ->setReleaseDate($item->getReleaseDate())
            ->setTitle($item->getTitle())
            ->setType($type)
            ->setVersion($item->getVersion())
        ;

        foreach ($item->getRelatedGenres() as $genre) {
            if (!is_string($genre)) {
                continue;
            }

            $media->addGenre(new Genre($genre));
        }

        return $media;
    }
}
