<?php

namespace Pyrex\AdminBundle\Controller;

use AppBundle\Service\SystemEmail;
use Pyrex\AdminBundle\Form\Type\DeejayRegistrationType;
use Pyrex\AdminBundle\Form\Type\LoginType;
use Pyrex\CoreModelBundle\Entity\Deejay;
use Pyrex\CoreModelBundle\Repository\DeejayRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Class RegistrationController
 * @author Christophe Pyree <christophe.pyree@gmail.com>
 * @package CertificationBundle\Controller
 * @Route(path="/user", service="app.registration_controller")
 */
class RegistrationController
{
    /**
     * @var FormFactory
     */
    private $formFactory;
    /**
     * @var TwigEngine
     */
    private $twig;
    /**
     * @var DeejayRepository
     */
    private $deejayRepository;
    /**
     * @var UserPasswordEncoder
     */
    private $securityPasswordEncoder;
    /**
     * @var SystemEmail
     */
    private $systemEmail;
    /**
     * @var Session
     */
    private $session;
    /**
     * @var Router
     */
    private $router;

    public function __construct(
        FormFactory $formFactory,
        TwigEngine $twig,
        DeejayRepository $deejayRepository,
        UserPasswordEncoder $encoder,
        SystemEmail $systemEmail,
        Session $session,
        Router $router
    )
    {
        $this->formFactory = $formFactory;
        $this->twig = $twig;
        $this->deejayRepository = $deejayRepository;
        $this->securityPasswordEncoder = $encoder;
        $this->systemEmail = $systemEmail;
        $this->session = $session;
        $this->router = $router;
    }

    /**
     * @Route(path="/register", name="register")
     * @Template()
     * @param Request $request
     * @return array|RedirectResponse
     */
    public function registrationAction(Request $request)
    {
        $deejay = new Deejay();
        $registrationForm = $this->formFactory->create(DeejayRegistrationType::class, $deejay);
        $registrationForm->handleRequest($request);
        if ($registrationForm->isSubmitted() && $registrationForm->isValid()) {
            $deejay->setSalt(base64_encode(microtime().random_bytes(15)));
            $deejay->setPassword($this->securityPasswordEncoder->encodePassword($deejay, $deejay->getPassword()));
            $deejay->addRole(Deejay::ROLE_USER);
            $this->deejayRepository->generateActivationToken($deejay);
            $this->deejayRepository->save($deejay);
            $this->session->getFlashBag()->set('success', 'deejay_registration_type.success_flash_message');
            $this->session->set('savedDeejayId', $deejay->getId());

            return new RedirectResponse($this->router->generate('registration_confirmation'));
        }

        return [
            'registrationForm'  => $registrationForm->createView(),
        ];
    }

    /**
     * @Route(path="/register/confirmation", name="registration_confirmation",methods={"GET"})
     * @Route(path="/register/confirmation/resend", name="registration_confirmation_resend",methods={"GET"})
     * @Template()
     * @param Request $request
     * @return array|RedirectResponse
     */
    public function registrationConfirmationAction(Request $request)
    {
        $deejayId = $this->session->get('savedDeejayId');
        $registerRedirect = new RedirectResponse($this->router->generate('register'));
        $deejay = null;

        if (!$deejayId || !$deejay = $this->deejayRepository->find($deejayId)) {
            return $registerRedirect;
        }

        if ($request->get('_route') === 'registration_confirmation_resend') {
            $this->systemEmail->newRegistrationMail($deejay);
            $this->session->getFlashBag()->set('success', 'deejay_registration_type.activation_resend');

            return new RedirectResponse($this->router->generate('registration_confirmation'));
        }

        dump($deejay);
        return [
            'deejay' => $deejay
        ];
    }

    /**
     * @Route("/new_user_mail/{activationToken}")
     * @Template()
     * @param Deejay $deejay
     * @return array
     */
    public function activeUserAction(Deejay $deejay)
    {
        $this->systemEmail->newRegistrationMail($deejay);
        dump($this->twig->render('PyrexAdminBundle:Authentification:activeUser.html.twig', [ 'deejay' => $deejay ]));
    }
}
