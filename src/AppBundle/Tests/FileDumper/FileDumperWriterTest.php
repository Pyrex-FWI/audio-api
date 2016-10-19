<?php

use AppBundle\Entity\Media;
use AppBundle\Event\DirectoryEvent;
use DeejayPoolBundle\Entity\AvdItem;
use DeejayPoolBundle\Entity\FranchisePoolItem;
use DeejayPoolBundle\Entity\SvItem;
use DeejayPoolBundle\Event\ItemDownloadEvent;
use DeejayPoolBundle\Event\PostItemsListEvent;
use DeejayPoolBundle\Event\ProviderEvents;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Faker\Generator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use AppBundle\FileDumper\FileDumperWriter;
use org\bovigo\vfs\vfsStream;
/**
 * FileDumperTest.
 */
class FileDumperTest extends KernelTestCase
{
    /** @var  Container */
    private $container;
    /** @var FileDumperWriter */
    private $fileDumper;

    protected function setUp()
    {
        static::bootKernel();
        $this->container    = static::$kernel->getContainer();
        $this->fileDumper   = $this->container->get('filedumper.writer.test');
    }

    /**
     * @test
     * @return
     */
    public function writerConf()
    {
        $testConf = $this->container->getParameter('collection.paths')['test'];
        $this->assertEquals($testConf['paths'], $this->fileDumper->getPaths());
        $this->assertContains($testConf['paths'][0], $this->fileDumper->getCmd($testConf['paths'][0]));
        $this->assertContains($testConf['match'], $this->fileDumper->getCmd($testConf['paths'][0]));
    }

    /**
     * @test
     * @return
     */
    public function read()
    {
        $fileName = $this->fileDumper->getFilePathName();
        vfsStream::setup($this->container->getParameter('kernel.cache_dir'));
        $file = vfsStream::url($fileName);
        
    }

}
