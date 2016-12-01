<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Media;
use AppBundle\Form\Type\EditMediaType;
use AppBundle\Form\Type\MediaFilterType;
use Pyrex\CoreModelBundle\Entity\Genre;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;


/**
 * Class MediaController
 * @author Christophe Pyree <christophe.pyree@gmail.com>
 * @package AppBundle\Controller
 * @Route("/media")
 * @Cache(public="true", maxage="3600", smaxage="3600")
 */
class MediaController extends Controller
{
    /**
     * @Route("/", name="media_list")
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
     * @Route("/{genre}/{year}", name="media_list_by_genre_year", defaults={"year"= null}, requirements={"year"="\d{4}"})
     * @Route("/{year}/{genre}", name="media_list_by_year_genre", defaults={"year"= null}, requirements={"year"="\d{4}"})
     * @ParamConverter(name="genre", options={"repository_method"="findOneBySlug"})
     * @Template("AppBundle:Media:index.html.twig")
     * @param Request $request
     * @param Genre   $genre
     * @param integer $year
     * @return Response
     */
    public function indexByGenreYearAction(Request $request, Genre $genre = null, $year = null)
    {
        $filterForm = $this->get('form.factory')->createNamed('f', MediaFilterType::class, null, ['method' => 'GET']);

        $dataFilter = [];
        if ($genre) {
            $dataFilter['genres'][] = $genre->getId();
        }
        if ($year) {
            $dataFilter['year'] = $year;
        }

        $filterForm->submit($dataFilter);
        $pagination = $this->get('repository.media')->paginate($request, $filterForm);

        return [
            'pagination' => $pagination,
            'filterForm' => $filterForm->createView(),
        ];
    }

    /**
     * @Route("/edit/{id}", requirements={"id": "\d+"}, name="media_id")
     * @Route("/edit/{slug}", name="media_slug")
     * @Route("/edit/{genreSlug}/{year}/{slug}", name="media_genre_year_slug")
     * @Template()
     * @return Response
     */
    public function editAction(Request $request, Media $media, $genreSlug = null, $year)
    {
        $form = $this->get('form.factory')->create(EditMediaType::class, $media);
        $form->handleRequest($request);

        return [
            'form'  => $form->createView(),
            'media' => $media,
        ];
    }

}
