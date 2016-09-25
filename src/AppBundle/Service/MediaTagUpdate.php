<?php

namespace AppBundle\Service;

use AppBundle\Entity\Media;
use AppBundle\Serializer\Normalizer\Id3MetadataNormalizer;
use Doctrine\ORM\EntityManagerInterface;
use Pyrex\CoreModelBundle\Repository\MediaRepository;
use Sapar\Id3\Metadata\Id3Metadata;
use Symfony\Component\Serializer\Serializer;

/**
 * Class MediaTagUpdate
 * @package AppBundle\Service
 */
class MediaTagUpdate
{

    /** @var  MediaRepository */
    private $mediaRepository;
    /** @var  Serializer */
    private $serializer;

    /**
     * MediaTagUpdate constructor.
     * @param MediaRepository $mediaRepository
     * @param Serializer      $serializer
     */
    public function __construct(MediaRepository $mediaRepository, Serializer $serializer)
    {
        $this->mediaRepository  = $mediaRepository;
        $this->serializer       = $serializer;
    }

    /**
     * @param Id3Metadata $id3Metadata
     * @param null|int    $providerId
     * @return bool
     */
    public function update(Id3Metadata $id3Metadata, $providerId = null, $mediaRef = null)
    {
        /* @var Media $media */
        $existMedia = new Media();

        if ($providerId) {
            $existMedia = MediaRepository::getMediaInstanceByProviderId($providerId);
        }
        if ($mediaRef) {
            $existMedia->setId($mediaRef);
        }
        $media = $this->serializer->denormalize($id3Metadata, get_class($existMedia), Id3Metadata::class, [Id3MetadataNormalizer::ORIGINAL_OBJECT => $existMedia]);

        try {
            $this->mediaRepository->merge($media);
        } catch (\Exception $e) {
            var_dump($e->getMessage());

            return false;
        }

        return true;
    }

}