<?php

namespace Pyrex\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class UserController extends Controller
{
    /**
     * @Route("/users", name="user_list")
     */
    public function indexAction()
    {
        return $this->render(
            'PyrexAdminBundle:User:index.html.twig',
            [
                'users' => $this->get('repository.deejay')->findAll(),
            ]
        );
    }
}
