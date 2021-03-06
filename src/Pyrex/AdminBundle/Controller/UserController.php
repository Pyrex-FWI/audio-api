<?php

namespace Pyrex\AdminBundle\Controller;

use Pyrex\AdminBundle\Form\Type\CreateDeejayType;
use Pyrex\CoreModelBundle\Entity\Deejay;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserController
 * @author Christophe Pyree <christophe.pyree@gmail.com>
 * @package Pyrex\AdminBundle\Controller
 * @Route(path="/user", service="app.user_controller")
 * Security("has_role('ROLE_ADMIN')")
 */
class UserController extends Controller
{

    /** @var \Twig_Environment  */
    private $twig;

    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @Route("/", name="user_list")
     * @return Response
     */
    public function indexAction()
    {
        return new Response(
            $this->twig->render(
                'PyrexAdminBundle:User:index.html.twig',
                [
                    'users' => $this->get('repository.deejay')->findAll(),
                ]
            )
        );
    }

    /**
     * @Route("/activate/{activationToken}", name="user_activate")
     * @param Deejay $deejay
     * @return RedirectResponse
     */
    public function activateAction(Deejay $deejay)
    {
        if (!(new \DateTime('now') < $deejay->getActivationTokenDate())) {
            $this->get('logger')->err(sprintf('Le token n\'est plus valide pour %s', $deejay->getEmail()), [$deejay]);
        }

        $this->get('repository.deejay')->activate($deejay);

        if ($deejay->getEnabled()) {
            $this->addFlash('success', 'User has been correctly enabled');
        }

        if (!$deejay->getEnabled()) {
            $this->addFlash('error', 'Error, user not enabled');
        }

        return $this->redirectToRoute('home');
    }

    /**
     * @param Request $request
     * @Route("/create", name="create_user")
     * @Template()
     * Todo: restrict to ROLE_ADMIN
     * @return array|RedirectResponse
     */
    public function createUserAction(Request $request)
    {
        $deejay = new Deejay();
        $createDeejayForm = $this->get('form.factory')->create(CreateDeejayType::class, $deejay);
        $createDeejayForm->handleRequest($request);
        if ($createDeejayForm->isSubmitted() && $createDeejayForm->isValid()) {
            $this->get('repository.deejay')->save($deejay);
            $this->addFlash('success', 'Deejay has been successfully created');

            return $this->redirectToRoute('user_list');
        }

        return [
            'form'  => $createDeejayForm->createView(),
        ];
    }

    /**
     * @param Request $request
     * @param Deejay  $deejay
     * @Route("/edit/{id}", name="user_edit")
     * @Template()
     * @return array|RedirectResponse
     */
    public function editAction(Request $request, Deejay $deejay)
    {
        $createDeejayForm = $this->get('form.factory')->create(CreateDeejayType::class, $deejay);
        $createDeejayForm->handleRequest($request);
        if ($createDeejayForm->isSubmitted() && $createDeejayForm->isValid()) {
            $this->get('repository.deejay')->save($deejay);
            $this->addFlash('success', 'Deejay has been successfully updated');

            return $this->redirectToRoute('user_list');
        }

        return [
            'form'  => $createDeejayForm->createView(),
        ];
    }
}
