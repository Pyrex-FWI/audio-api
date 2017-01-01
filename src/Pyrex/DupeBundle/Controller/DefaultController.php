<?php

namespace Pyrex\DupeBundle\Controller;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Pyrex\DupeBundle\Entity\DupeFile;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @param $groupId
     * @Route("/{groupId}", name="dupe_index", defaults={"groupId" = 1})
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction($groupId)
    {
        /** @var Registry $doctrine */
        $doctrine = $this->get('doctrine');
        /** @var EntityManager $entityManager */
        $entityManager = $doctrine->getManager('pyrex_dupe');
        $dupeGroupRepo = $entityManager->getRepository('PyrexDupeBundle:DupeGroup');
        $groupId = $dupeGroupRepo->find($groupId);

        return $this->render('PyrexDupeBundle:Default:index.html.twig', array('group' => $groupId));
    }

    /**
     * @Route("/mark-file-for-delete/{fileId}", name="markToDelete" )
     *
     * @param $fileId
     * @param $request
     *
     * @return JsonResponse
     */
    public function markFileForDelete($fileId, Request $request)
    {
        $doctrine = $this->get('doctrine');
        /** @var EntityManager $entityManager */
        $entityManager = $doctrine->getManager('pyrex_dupe');
        /** @var DupeFile $fileDupe */
        $fileDupe = $entityManager->getRepository('PyrexDupeBundle:DupeFile')->find($fileId);

        if ($fileDupe) {
            $fileDupe->setDeleteFlag(true);
            $entityManager->persist($fileDupe);
            $entityManager->flush();
        }
        $referer = $request->headers->get('referer');
        if ($referer) {
            return $this->redirect($referer);
        } else {
            return $this->redirect($this->generateUrl('dupe_index', ['groupId' => $fileDupe->getDupeGroup()->getId()]));
        }
        //return new JsonResponse(['status' => $fileDupe ? true:false]);
    }
}
