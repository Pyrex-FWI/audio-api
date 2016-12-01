<?php

namespace AppBundle;

use AppBundle\DependencyInjection\Compiler\CollectionDumperPass;
use AppBundle\DependencyInjection\Compiler\FileOrganizerRuleCompilerPass;
use Symfony\Bundle\MonologBundle\DependencyInjection\Compiler\LoggerChannelPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AppBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new FileOrganizerRuleCompilerPass());
        $container->addCompilerPass(new CollectionDumperPass());
    }
}
