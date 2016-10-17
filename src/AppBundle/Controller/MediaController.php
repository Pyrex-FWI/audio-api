<?php
/**
 * Copyright (c) 2016. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
 * Morbi non lorem porttitor neque feugiat blandit. Ut vitae ipsum eget quam lacinia accumsan.
 * Etiam sed turpis ac ipsum condimentum fringilla. Maecenas magna.
 * Proin dapibus sapien vel ante. Aliquam erat volutpat. Pellentesque sagittis ligula eget metus.
 * Vestibulum commodo. Ut rhoncus gravida arcu.
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Media;
use AppBundle\Form\Type\EditMediaType;
use AppBundle\Form\Type\MediaFilterType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class MediaController
 * @author Christophe Pyree <christophe.pyree@gmail.com>
 * @package AppBundle\Controller
 * @Route("/media")
 */
class MediaController extends Controller
{
    /**
     * @Route("/")
     * @Template()
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $filterForm = $this->get('form.factory')->createNamed('f', MediaFilterType::class, null, ['method' => 'GET']);

        $pagination = $this->get('repository.media')->paginate($request, $filterForm);

        return [
            'pagination' => $pagination,
            'filterForm' => $filterForm->createView(),
        ];
    }

    /**
     * @Route("/edit/{id}")
     * @Template()
     * @return Response
     */
    public function editAction(Request $request, Media $media)
    {
        $form = $this->get('form.factory')->create(EditMediaType::class, $media);
        $form->handleRequest($request);

        return ['form' => $form->createView()];
    }

}
