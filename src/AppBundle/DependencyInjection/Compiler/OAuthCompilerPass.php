<?php
/**
 * Copyright (c) 2016. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
 * Morbi non lorem porttitor neque feugiat blandit. Ut vitae ipsum eget quam lacinia accumsan.
 * Etiam sed turpis ac ipsum condimentum fringilla. Maecenas magna.
 * Proin dapibus sapien vel ante. Aliquam erat volutpat. Pellentesque sagittis ligula eget metus.
 * Vestibulum commodo. Ut rhoncus gravida arcu.
 */

namespace AppBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class OAuthCompilerPass implements CompilerPassInterface
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
        if ($container->hasDefinition('hwi_oauth.registration.form')) {
            return;
        }
        //$definition = $container->getDefinition('deejay_new_registration_form');
        //$container->setDefinition('hwi_oauth.registration.form', $definition);
        $oauthUtils = $container->getDefinition('hwi_oauth.security.oauth_utils');
        $oauthUtils->replaceArgument(2, true); // generate connect urls instead of login urls when user is logged in
        $container->setParameter('hwi_oauth.connect', true); // enable connect functionality, without requiring a specific registration form
    }
}
