<?php

namespace AppBundle\DependencyInjection\Compiler;

use AppBundle\DeejayFilesOrganizerBundle;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class CollectionDumperPass implements CompilerPassInterface
{

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container)
    {
        $fPaths     = $container->getParameter('collection.paths');
        $findCmd    = $container->getParameter('find.command');
        $outputPath = $container->getParameter('kernel.cache_dir');

        if (!$container->hasDefinition('app.command.collection_dumper')) {
            return;
        }

        $collectionDumperCommand = $container->getDefinition('app.command.collection_dumper');

        if ($fPaths && $findCmd && $outputPath) {

            foreach ($fPaths as $name => $bag) {
                $name = 'filedumper.writer.'.$name;
                $fdWriter = (new Definition(
                    'AppBundle\\FileDumper\\FileDumperWriter',
                    [$name, $bag['provider'], $findCmd, $bag['paths'], $bag['match'], $outputPath]
                ))->addTag('filedumper.writer');
                $container->setDefinition(
                    $name,
                    $fdWriter
                );

                $collectionDumperCommand->addMethodCall('addFileDumperWriter', [new Reference($name)]);
            }
        }
    }
}