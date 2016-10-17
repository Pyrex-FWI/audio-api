<?php
ini_set('memory_limit', -1);
set_time_limit(3600);

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

/**
 * Class AppKernel
 */
class AppKernel extends Kernel
{
    /**
     * {@inheritdoc}
     */
    public function registerBundles()
    {
        $bundles = [
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new AppBundle\AppBundle(),
            new \HitsBundle\HitsBundle(),
            new \DeejayPoolBundle\DeejayPoolBundle(),
            new Pyrex\RadioBundle\PyrexRadioBundle(),
            new Pyrex\CoreModelBundle\PyrexCoreModelBundle(),
            new OldSound\RabbitMqBundle\OldSoundRabbitMqBundle(),
            new Bazinga\Bundle\FakerBundle\BazingaFakerBundle(),
            new Phobetor\RabbitMqSupervisorBundle\RabbitMqSupervisorBundle(),
            new CertificationBundle\CertificationBundle(),
            new Pyrex\AdminBundle\PyrexAdminBundle(),
            new \Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            new \Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
            new Lexik\Bundle\FormFilterBundle\LexikFormFilterBundle(),
        ];


        if (in_array($this->getEnvironment(), ['dev', 'test'], true)) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
            $bundles[] = new \Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle();
            $bundles[] = new \Hautelook\AliceBundle\HautelookAliceBundle();
        }

        return $bundles;
    }

    /**
     * {@inheritdoc}
     */
    public function getRootDir()
    {
        return __DIR__;
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheDir()
    {
        return dirname(__DIR__).'/var/cache/'.$this->getEnvironment();
    }

    /**
     * {@inheritdoc}
     */
    public function getLogDir()
    {
        return dirname(__DIR__).'/var/logs';
    }

    /**
     * {@inheritdoc}
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }
}
