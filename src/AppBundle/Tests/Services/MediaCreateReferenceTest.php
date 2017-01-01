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
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Prophecy\Prophet;
use Pyrex\CoreModelBundle\Repository\MediaRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class MediaCreateReference.
 *
 * @author Christophe Pyree <christophe.pyree@gmail.fr>
 */
class MediaCreateReferenceTest extends KernelTestCase
{
    /** @var EntityManagerInterface */
    private $entityManager;
    /** @var Prophet */
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
        /* @var Media $media */
        $media = new Media();
        $media->setFullPath('toto');

        $mediaRepository = $this->prophet->prophesize(MediaRepository::class);
        $mediaRepository->createIfNotExist('toto', null)->shouldBeCalled()->willReturn($media);
        $mediaRepository->findOneBy(['fullFilePathMd5' => md5('toto')])->shouldBeCalled();

        $entityManager = $this->prophet->prophesize(EntityManager::class);
        $entityManager->getRepository('AppBundle:Media')->willReturn($mediaRepository);
        $entityManager->persist($media)->shouldBeCalled();
        $entityManager->flush()->shouldBeCalled();

        $mediaCreateReference = new MediaCreateReference($mediaRepository->reveal());
        $result = $mediaCreateReference->createReferenceIfNotExist('toto');
        $this->assertEquals(Media::class, get_class($result));
    }
}
