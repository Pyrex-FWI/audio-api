<?php

namespace AppBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Class AppExtension.
 *
 * @author Christophe Pyree <christophe.pyree@gmail.com>
 */
class AppExtension extends Extension implements PrependExtensionInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function prepend(ContainerBuilder $container)
    {
        $bundles = $container->getParameter('kernel.bundles');
        $config = array('channels' => ['core_entity']);
        foreach ($container->getExtensions() as $name => $extension) {
            switch ($name) {
                case 'monolog':
                    $container->prependExtensionConfig($name, $config);
                    break;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $this->buildIndexingWorkflow($config, $container);
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return 'app';
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    private function buildIndexingWorkflow(array $config, ContainerBuilder $container)
    {
        $container->setParameter(
            'app.library.indexing.workflow.create_media_reference_before_read_tag',
            $config['library']['indexing_workflow']['create_media_reference_before_read_tag']
        );
    }
}
