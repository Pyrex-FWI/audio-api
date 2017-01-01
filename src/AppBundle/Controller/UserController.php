<?php
/**
 * Copyright (c) 2016. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
 * Morbi non lorem porttitor neque feugiat blandit. Ut vitae ipsum eget quam lacinia accumsan.
 * Etiam sed turpis ac ipsum condimentum fringilla. Maecenas magna.
 * Proin dapibus sapien vel ante. Aliquam erat volutpat. Pellentesque sagittis ligula eget metus.
 * Vestibulum commodo. Ut rhoncus gravida arcu.
 */

namespace AppBundle\Controller;

use AppBundle\Form\Type\CreateDeejayType;
use Pyrex\CoreModelBundle\Entity\Deejay;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class UserController.
 *
 * @author Christophe Pyree <christophe.pyree@gmail.com>
 * @Route("/user")
 * @Security("has_role('ROLE_ADMIN')")
 */
class UserController extends Controller
{
    /**
     * @Route("/", name="user_list")
     *
     * @return array
     */
    public function indexAction()
    {
        return $this->render(
            'AppBundle:User:index.html.twig',
            [
                'users' => $this->get('repository.deejay')->findAll(),
            ]
        );
    }

    /**
     * @Route("/activate/{id}", name="user_activate")
     *
     * @param Deejay $deejay
     *
     * @return RedirectResponse
     */
    public function activateAction(Deejay $deejay)
    {
        $this->get('repository.deejay')->activate($deejay);

        if ($deejay->getEnabled()) {
            $this->addFlash('succes', 'User has been correctly enabled');
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
     *
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
            'form' => $createDeejayForm->createView(),
        ];
    }

    /**
     * @param Request $request
     * @param Deejay  $deejay
     * @Route("/edit/{id}", name="user_edit")
     * @Template()
     *
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
            'form' => $createDeejayForm->createView(),
        ];
    }
}
