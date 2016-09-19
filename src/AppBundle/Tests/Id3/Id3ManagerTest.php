<?php

namespace AppBundle\Id3;

use Sapar\Id3\Metadata\Id3Metadata;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Container;

class Id3ManagerTest extends KernelTestCase
{
    /** @var  Container */
    private $container;

    protected function setUp()
    {
        static::bootKernel();
        $this->container = static::$kernel->getContainer();
    }

    public function testManager()
    {
        $manager = new Id3Manager();
        $manager->addWrapper($this->container->get('id3v2_wrapper'));
        $manager->addWrapper($this->container->get('eyed3_wrapper'));
        $manager->addWrapper($this->container->get('media_wrapper'));

        $id3Meta = new Id3Metadata(realpath($this->container->getParameter('allowed_directories')[0].DIRECTORY_SEPARATOR.'dir01/01.mp3'));
        $manager->read($id3Meta);
        $this->assertEquals('Nom de l\'album', $id3Meta->getAlbum());
        $this->assertEquals('Nom du morceau', $id3Meta->getTitle());
        $this->assertEquals('sample of full track', $id3Meta->getComment());
        $this->assertEquals(true, $id3Meta->getYear() > 1900);

        $id3Meta = new Id3Metadata(realpath($this->container->getParameter('allowed_directories')[0].DIRECTORY_SEPARATOR.'dir01/04.flac'));
        $manager->read($id3Meta);
        $this->assertEquals('Flac album', $id3Meta->getAlbum());
        $this->assertEquals('Flac title', $id3Meta->getTitle());
        $this->assertEquals(true, $id3Meta->getYear() > 1900);
    }
}
