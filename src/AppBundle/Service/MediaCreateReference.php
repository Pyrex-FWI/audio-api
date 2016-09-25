<?php

namespace AppBundle\Service;

use AppBundle\Entity\Media;
use AppBundle\Serializer\Normalizer\Id3MetadataNormalizer;
use Doctrine\ORM\EntityManagerInterface;
use Pyrex\CoreModelBundle\Repository\MediaRepository;
use Sapar\Id3\Metadata\Id3Metadata;
use Symfony\Component\Serializer\Serializer;

/**
 * Class MediaCreateReference
 * @author Christophe Pyree <christophe.pyree@gmail.fr>
 * @package AppBundle\Service
 */
class MediaCreateReference
{

    /** @var  MediaRepository */
    private $mediaRepository;

    /**
     * MediaTagUpdate constructor.
     * @param MediaRepository $mediaRepository
     */
    public function __construct(MediaRepository $mediaRepository)
    {
        $this->mediaRepository = $mediaRepository;
    }

    /**
     * @param string   $file
     * @param null|int $providerId
     * @return Media
     */
    public function createReferenceIfNotExist($file, $providerId = null)
    {
        $media = $this->mediaRepository->createIfNotExist($file, $providerId);

        return $media;
    }

}