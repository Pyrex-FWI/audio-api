<?php

namespace AppBundle\Serializer\Normalizer;

use AppBundle\Entity\Media;
use AppBundle\Entity\Provider\AvDistrictMedia;
use AppBundle\Entity\Provider\DigitalDjPoolMedia;
use AppBundle\Entity\Provider\FranchiseAudioMedia;
use AppBundle\Entity\Provider\FranchiseVideoMedia;
use AppBundle\Entity\Provider\SmashVidzMedia;
use Doctrine\Common\Collections\ArrayCollection;
use Pyrex\CoreModelBundle\Entity\Genre;
use Pyrex\CoreModelBundle\Repository\GenreRepository;
use Sapar\Id3\Metadata\Id3Metadata;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\scalar;

/**
 * Class Id3MetadataNormalizer
 * @package AppBundle\Serializer\Normalizer
 */
class Id3MetadataNormalizer extends AbstractNormalizer
{
    const ORIGINAL_OBJECT = 'original_object';

    /** @var  GenreRepository */
    private $genreRepository;

    /**
     * @param GenreRepository $genreRepository
     */
    public function setGenreRepository(GenreRepository $genreRepository)
    {
        $this->genreRepository = $genreRepository;
    }
    /**
     * Denormalizes data back into an object of the given class.
     *
     * @param Id3Metadata $data    data to restore
     * @param string      $class   the expected class to instantiate
     * @param string      $format  format the given data was extracted from
     * @param array       $context options available to the denormalizer
     *
     * @return Media
     */
    public function denormalize($data, $class, $format = null, array $context = array())
    {
        /** @var Media $media */
        $media = isset($context[self::ORIGINAL_OBJECT]) && is_object($context[self::ORIGINAL_OBJECT]) ? $context[self::ORIGINAL_OBJECT] : new $class();

        $media->setTitle($data->getTitle());
        $media->setArtist($data->getArtist());
        $media->setFullPath($data->getFile()->__toString());
        $media->setBpm($data->getBpm());
        if ($data->getGenre()) {
            $media->addGenre($this->genreRepository->createIfNotExist($data->getGenre()));
        }
        $media->setYear($data->getYear());

        return $media;
    }

    /**
     * Checks whether the given class is supported for denormalization by this normalizer.
     *
     * @param mixed  $data   Data to denormalize from
     * @param string $type   The class to which the data should be denormalized
     * @param string $format The format being deserialized from
     *
     * @return bool
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        if (!is_object($data)) {
            return false;
        }
        $allowedMedias = [
            Media::class,
            AvDistrictMedia::class,
            SmashVidzMedia::class,
            DigitalDjPoolMedia::class,
            FranchiseAudioMedia::class,
            FranchiseVideoMedia::class,
        ];
        if (Id3Metadata::class === $format) {
            if (in_array($type, $allowedMedias)) {
                return true;
            }
        }
    }

    /**
     * Normalizes an object into a set of arrays/scalars.
     *
     * @param object $object  object to normalize
     * @param string $format  format the normalization result will be encoded as
     * @param array  $context Context options for the normalizer
     *
     * @return array|scalar
     */
    public function normalize($object, $format = null, array $context = array())
    {
        // TODO: Implement normalize() method.
    }

    /**
     * Checks whether the given class is supported for normalization by this normalizer.
     *
     * @param mixed  $data   Data to normalize
     * @param string $format The format being (de-)serialized from or into
     *
     * @return bool
     */
    public function supportsNormalization($data, $format = null)
    {
        // TODO: Implement supportsNormalization() method.
    }
}