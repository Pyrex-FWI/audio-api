<?php

namespace Pyrex\RadioBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('PyrexRadioBundle:Default:index.html.twig');
    }
}
