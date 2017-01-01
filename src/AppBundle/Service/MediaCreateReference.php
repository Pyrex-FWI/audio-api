<?php

namespace AppBundle\Service;

use AppBundle\Entity\Media;
use Pyrex\CoreModelBundle\Repository\MediaRepository;

/**
 * Class MediaCreateReference.
 *
 * @author Christophe Pyree <christophe.pyree@gmail.fr>
 */
class MediaCreateReference
{
    /** @var MediaRepository */
    private $mediaRepository;

    /**
     * MediaTagUpdate constructor.
     *
     * @param MediaRepository $mediaRepository
     */
    public function __construct(MediaRepository $mediaRepository)
    {
        $this->mediaRepository = $mediaRepository;
    }

    /**
     * @param string   $file
     * @param null|int $providerId
     *
     * @return Media
     */
    public function createReferenceIfNotExist($file, $providerId = null)
    {
        $media = $this->mediaRepository->createIfNotExist($file, $providerId);

        return $media;
    }
}
