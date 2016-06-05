<?php

namespace AppBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class FileOrganizerRuleCompilerPass implements CompilerPassInterface
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
        $tagName      = 'organizer.rule';
        $mediaFileOrg = 'app.media.organizer.manager';

        if (!$container->hasDefinition($mediaFileOrg)) {
            return;
        }

        $providerManager = $container->getDefinition($mediaFileOrg);
        $taggedServices  = $container->findTaggedServiceIds($tagName);

        foreach ($taggedServices as $id => $attributes) {
            $providerManager->addMethodCall(
                'addRule',
                [
                    new Reference($id),
                ]
            );
        }
    }
}
