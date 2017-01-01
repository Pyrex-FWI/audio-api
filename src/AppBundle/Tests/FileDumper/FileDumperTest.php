<?php

namespace AppBundle\Tests\FileDumper;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use AppBundle\FileDumper\FileDumperWriter;
use AppBundle\FileDumper\FileDumperReader;
use AppBundle\FileDumper\FileDumperRow;

/**
 * FileDumperTest.
 */
class FileDumperTest extends KernelTestCase
{
    /** @var Container */
    private $container;
    /** @var FileDumperWriter */
    private $fileDumper;
    private $testconf;

    protected function setUp()
    {
        static::bootKernel();
        $this->container = static::$kernel->getContainer();
        $this->fileDumper = $this->container->get('filedumper.writer.test');
        $this->testConf = $this->container->getParameter('collection.paths')['test'];
    }

    /**
     * @test
     *
     * @return
     */
    public function writerConf()
    {
        $this->assertEquals($this->testConf['paths'], $this->fileDumper->getPaths());
        $this->assertContains($this->testConf['paths'][0], $this->fileDumper->getCmd($this->testConf['paths'][0]));
        $this->assertContains($this->testConf['match'], $this->fileDumper->getCmd($this->testConf['paths'][0]));
    }

    /**
     * [writeTestDumpFile description].
     *
     * @param [type] $outFile  [description]
     * @param [type] $path     [description]
     * @param [type] $provider [description]
     * @param int    $nbFiles  [description]
     *
     * @return [type] [description]
     */
    public static function writeTestDumpFile($outFile, $path, $provider, $nbFiles = 20)
    {
        $fileData = '';
        $faker = static::$kernel->getContainer()->get('hautelook_alice.faker');
        for ($i = 0; $i < $nbFiles; ++$i) {
            $mediaFileName[$i] = $path.DIRECTORY_SEPARATOR.$faker->mediaFileName();
            $fileData .= sprintf('"%s","%d"', $mediaFileName[$i], $provider).PHP_EOL;
        }
        file_put_contents($outFile, $fileData);

        return $fileData;
    }

    /**
     * @test
     * @depends writerConf
     *
     * @return
     */
    public function read()
    {
        $fileName = $this->fileDumper->getFilePathName();
        $faker = static::$kernel->getContainer()->get('hautelook_alice.faker');
        $nbFiles = 20;
        $fileData = static::writeTestDumpFile($fileName, $this->testConf['paths'][0], $this->testConf['provider']);
        $reader = new FileDumperReader($fileName);
        $this->assertEquals($nbFiles, $reader->count());
        foreach ($reader as $line) {
            $this->assertInstanceOf(FileDumperRow::class, $line);
            $this->assertContains($line->getFile()->getPathName(), $fileData);
            break;
        }
    }
}
