<?php
/**
 * Copyright (c) 2016. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
 * Morbi non lorem porttitor neque feugiat blandit. Ut vitae ipsum eget quam lacinia accumsan.
 * Etiam sed turpis ac ipsum condimentum fringilla. Maecenas magna.
 * Proin dapibus sapien vel ante. Aliquam erat volutpat. Pellentesque sagittis ligula eget metus.
 * Vestibulum commodo. Ut rhoncus gravida arcu.
 */

namespace AppBundle\Controller;

use AppBundle\Form\Type\EditGenreType;
use Pyrex\CoreModelBundle\Entity\Genre;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class GenreController.
 *
 * @author Christophe Pyree <christophe.pyree@gmail.com>
 * @Route("/genre")
 */
class GenreController extends Controller
{
    /**
     * @Route("/", name="genre_list")
     * @Template()
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $pagination = $this->get('repository.genre')->paginate($request);

        return [
            'pagination' => $pagination,
        ];
    }

    /**
     * @Route("/edit/{id}", requirements={"id": "\d+"}, name="genre_id")
     * @Route("/edit/{slug}", name="genre_slug")
     * @Template()
     *
     * @return Response
     */
    public function editAction(Request $request, Genre $genre)
    {
        $form = $this->get('form.factory')->create(EditGenreType::class, $genre);
        $form->handleRequest($request);

        return [
            'form' => $form->createView(),
            'genre' => $genre,
        ];
    }
}
