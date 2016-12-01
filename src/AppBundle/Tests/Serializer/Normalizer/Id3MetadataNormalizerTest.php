<?php

namespace AppBundle\Tests\Serializer\Normalizer;

use AppBundle\Entity\Media;
use AppBundle\Entity\Provider\AvDistrictMedia;
use AppBundle\Entity\Provider\DigitalDjPoolMedia;
use AppBundle\Entity\Provider\FranchiseAudioMedia;
use AppBundle\Entity\Provider\FranchiseVideoMedia;
use AppBundle\Entity\Provider\SmashVidzMedia;
use AppBundle\Serializer\Normalizer\Id3MetadataNormalizer;
use Doctrine\Common\Collections\ArrayCollection;
use Sapar\Id3\Metadata\Id3Metadata;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\scalar;

/**
 * Class Id3MetadataNormalizer
 * @package AppBundle\Tests\Serializer\Normalizer
 */
class Id3MetadataNormalizerTest extends KernelTestCase
{
    /** @var  Container */
    private $container;
    protected static $testDir = 'id3';
    protected static $mediaFileName = 'my_fake_media.mp3';

    protected function setUp()
    {
        static::bootKernel();
        $this->container = static::$kernel->getContainer();
    }

    /**
     *
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        touch(realpath(sys_get_temp_dir()).DIRECTORY_SEPARATOR.self::$mediaFileName);
    }

    /**
     *
     */
    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
        unlink(realpath(sys_get_temp_dir()).DIRECTORY_SEPARATOR.self::$mediaFileName);
    }

    /**
     * @test
     */
    public function convertId3MetadataToMediaObject()
    {
        $id3Metadata = new Id3Metadata(realpath(sys_get_temp_dir()).DIRECTORY_SEPARATOR.self::$mediaFileName);
        $id3Metadata->setTitle('Title');

        /** @var Media $media */
        $media = $this->container->get('serializer')->denormalize($id3Metadata, Media::class, Id3Metadata::class, [Id3MetadataNormalizer::ORIGINAL_OBJECT => null]);
        $this->assertEquals('Title', $media->getTitle());
    }

}