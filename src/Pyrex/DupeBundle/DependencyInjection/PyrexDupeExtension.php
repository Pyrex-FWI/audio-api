<?php

namespace Pyrex\DupeBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Yaml\Yaml;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class PyrexDupeExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        if (isset($config['dupe_dump_file'])) {
            $container->setParameter('pyrex_dupe.dupe_dump_file', $config['dupe_dump_file']);
        }
        $container->setParameter('pyrex_dupe.extensions', $config['extensions']);
    }

    /**
     * Allow an extension to prepend the extension configurations.
     *
     * @param ContainerBuilder $container
     */
    public function prepend(ContainerBuilder $container)
    {
        $bundles = $container->getParameter('kernel.bundles');
        if (!$bundles['DoctrineBundle']) {
            return;
        }

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $configs = Yaml::parse($container->getParameter('kernel.root_dir').'/config/config.yml');
        if (!isset($configs['pyrex_dupe'])) {
            return;
        }

        $configs       = ['pyrex_dupe' => $configs['pyrex_dupe']];
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);

        //Register new connection
        $sqlite = [
            'dbal' => [
                'connections' => [
                    $config['database']['local_entity_manager_name'] => [
                        'driver' => $config['database']['driver'],
                        'path'   => $config['database']['path'],
                    ],
                ],
            ],
        ];
        $container->prependExtensionConfig('doctrine', $sqlite);

        //Register new entity manager
        $sqlite = [];
        $sqlite = [
            'orm' => [
                'entity_managers' => [
                    $config['database']['local_entity_manager_name'] => [
                        'connection' => $config['database']['local_entity_manager_name'],
                        'mappings'   => [
                            'PyrexDupeBundle' => [
                                'type'   => 'annotation',
                                'dir'    => '%kernel.root_dir%/../src/Pyrex/DupeBundle/Entity',
                                'prefix' => 'Pyrex\DupeBundle\Entity',
                            ],
                        ],
                    ],

                ],
            ],
        ];
        $container->prependExtensionConfig('doctrine', $sqlite);
    }
}
