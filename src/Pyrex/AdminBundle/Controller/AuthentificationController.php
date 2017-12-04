<?php
/**
 * Copyright (c) 2016. Lorem ipsum dolor sit amet, consectetur adipiscing elit. 
 * Morbi non lorem porttitor neque feugiat blandit. Ut vitae ipsum eget quam lacinia accumsan. 
 * Etiam sed turpis ac ipsum condimentum fringilla. Maecenas magna. 
 * Proin dapibus sapien vel ante. Aliquam erat volutpat. Pellentesque sagittis ligula eget metus. 
 * Vestibulum commodo. Ut rhoncus gravida arcu. 
 */

namespace Pyrex\AdminBundle\Controller;

use Pyrex\AdminBundle\Form\Type\LoginType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Class SecurityController
 * @author Christophe Pyree <christophe.pyree@gmail.com>
 * @package CertificationBundle\Controller
 * @Route(path="/", service="app.authentification_controller")
 */
class AuthentificationController
{
    /**
     * @var AuthenticationUtils
     */
    private $authenticationUtils;
    /**
     * @var FormFactory
     */
    private $formFactory;
    /**
     * @var TwigEngine
     */
    private $twig;

    public function __construct(
        AuthenticationUtils $authenticationUtils,
        FormFactory $formFactory,
        TwigEngine $twig
    )
    {
        $this->authenticationUtils = $authenticationUtils;
        $this->formFactory = $formFactory;
        $this->twig = $twig;
    }

    /**
     * @Route("/login", name="login")
     * @param Request $request
     * @Cache(expires="+2 minute", public=true)
     * @Template()
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loginAction(Request $request)
    {
    }

    /**
     * @Route("/login-form", name="login-form")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loginFormAction(Request $request)
    {
        $error = $this->authenticationUtils->getLastAuthenticationError();
        $lastUsername = $this->authenticationUtils->getLastUsername();

        $form = $this->formFactory->createNamed(null, LoginType::class);
        $form->get('_username')->setData($lastUsername);

        return new Response(
            $this->twig->render(
                'PyrexAdminBundle:Authentification:login-form.html.twig',
                [
                    // last username entered by the user
                    'last_username' => $lastUsername,
                    'error'         => $error,
                    'form'          => $form->createView(),
                ]
            )
        );
    }
}
