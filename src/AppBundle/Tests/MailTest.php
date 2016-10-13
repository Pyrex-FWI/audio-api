<?php

namespace AppBundle;

use AppBundle\Entity\Media;
use AppBundle\Serializer\Normalizer\Id3MetadataNormalizer;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Prophecy\Prophet;
use Pyrex\CoreModelBundle\Repository\MediaRepository;
use Sapar\Id3\Metadata\Id3Metadata;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Serializer\Serializer;

/**
 * Class MediaTagReaderTest
 * @author Christophe Pyree <christophe.pyree@gmail.com>
 * @package AppBundle\Service
 */
class MailTest extends KernelTestCase
{

    /** @var  Prophet */
    private $prophet;
    /** @var  Container */
    private $container;
    protected function setUp()
    {
        parent::setUp();
        static::bootKernel();
        $this->container = static::$kernel->getContainer();
    }

    /**
     * @test
     */
    public function mail()
    {
        $message = \Swift_Message::newInstance()
            ->setFrom('yemistikris@hotmail.fr')
            ->setTo('yemistikris@hotmail.fr')
            ->setSubject('Test  symfony')
            ->setBody(
                'Ici tu trouveras un text'
            );
        $this->container->get('logger')->info('send mail??');
        $failures = null;
        $res = $this->container->get('mailer')->send($message, $failures);
        dump($res);
        dump($failures);
    }

}