<?php
/**
 * Copyright (c) 2016. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
 * Morbi non lorem porttitor neque feugiat blandit. Ut vitae ipsum eget quam lacinia accumsan.
 * Etiam sed turpis ac ipsum condimentum fringilla. Maecenas magna.
 * Proin dapibus sapien vel ante. Aliquam erat volutpat. Pellentesque sagittis ligula eget metus.
 * Vestibulum commodo. Ut rhoncus gravida arcu.
 */

namespace AppBundle\Service;

use AppBundle\Entity\Media;
use AppBundle\Serializer\Normalizer\Id3MetadataNormalizer;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Prophecy\Prophet;
use Pyrex\CoreModelBundle\Repository\MediaRepository;
use Sapar\Id3\Metadata\Id3Metadata;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Serializer\Serializer;

/**
 * Class MediaTagReaderTest
 * @author Christophe Pyree <christophe.pyree@gmail.com>
 * @package AppBundle\Service
 */
class MediaTagReaderTest extends KernelTestCase
{

    /** @var  Prophet */
    private $prophet;
    protected function setUp()
    {
        parent::setUp();
        $this->prophet = new \Prophecy\Prophet();
    }

    /**
     * @test
     */
    public function createReferenceIfNotExist()
    {

    }

}