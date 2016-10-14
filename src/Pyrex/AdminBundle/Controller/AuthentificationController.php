<?php
/**
 * Copyright (c) 2016. Lorem ipsum dolor sit amet, consectetur adipiscing elit. 
 * Morbi non lorem porttitor neque feugiat blandit. Ut vitae ipsum eget quam lacinia accumsan. 
 * Etiam sed turpis ac ipsum condimentum fringilla. Maecenas magna. 
 * Proin dapibus sapien vel ante. Aliquam erat volutpat. Pellentesque sagittis ligula eget metus. 
 * Vestibulum commodo. Ut rhoncus gravida arcu. 
 */

namespace Pyrex\AdminBundle\Controller;

use Pyrex\AdminBundle\Form\Type\DeejayRegistrationType;
use Pyrex\CoreModelBundle\Entity\Deejay;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class SecurityController
 * @author Christophe Pyree <christophe.pyree@gmail.com>
 * @package CertificationBundle\Controller
 * @Route()
 */
class AuthentificationController extends Controller
{
    /**
     * @Route("/login", name="login")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loginAction(Request $request)
    {

        $authenticationUtils = $this->get('security.authentication_utils');
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError(); // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $form = $this->get('form.factory')->createNamedBuilder(null, FormType::class)
            ->add(
                '_username',
                TextType::class,
                [
                    'data' => $lastUsername,
                ]
            )
            ->add(
                '_password',
                PasswordType::class
            )
            ->add(
                'valider',
                SubmitType::class
            )
            ->getForm();


        return $this->render(
            'PyrexAdminBundle:Authentification:login.html.twig',
            [
                // last username entered by the user
                'last_username' => $lastUsername,
                'error'         => $error,
                'form'          => $form->createView(),
            ]
        );
    }

    /**
     * @Route(path="/register" ,name="register")
     * @Template()
     * @param Request $request
     * @return array
     */
    public function registerAction(Request $request)
    {
        $deejay = new Deejay();
        $registrationForm = $this->get('form.factory')->create(DeejayRegistrationType::class, $deejay);
        $registrationForm->handleRequest($request);
        if ($registrationForm->isSubmitted() && $registrationForm->isValid()) {
            $deejay->setPassword($this->get('security.password_encoder')->encodePassword($deejay, $deejay->getPassword()));
            $deejay->addRole('ROLE_USER');
            $this->get('repository.deejay')->save($deejay);
        }

        return [
            'registrationForm'  => $registrationForm->createView(),
        ];
    }

    /**
     * @Route("/new_user_mail/{id}")
     * @Template()
     * @param Deejay $deejay
     * @return array
     */
    public function activeUserAction(Deejay $deejay)
    {
        $this->get('app.system_email')->newRegistrationMail($deejay);
        dump($this->get('templating')->render('PyrexAdminBundle:Authentification:activeUser.html.twig', [ 'deejay' => $deejay ]));
    }
}
