<?php

namespace AppBundle\Controller;

use AppBundle\Service\Streamer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="home")
     *
     * @return Response
     */
    public function indexAction()
    {
        $parts = explode('/', file_get_contents($this->getParameter('kernel.root_dir').'/../.git/HEAD'));

        return new Response('Audio Api Version:'.trim(end($parts)));
    }

    /**
     * @Route("/stream", name="stream")
     * @Template()
     */
    public function streamAction(Request $request)
    {
        if (($file = $request->get('file')) == '') {
            throw $this->createNotFoundException('Resource not exist');
        }
        /** @var Streamer $streamer */
        $streamer = $this->get('streamer');

        return $streamer->start($file);
    }
}
