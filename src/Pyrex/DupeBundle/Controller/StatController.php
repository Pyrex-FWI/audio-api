<?php

namespace Pyrex\DupeBundle\Controller;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Pyrex\DupeBundle\Repository\DupeFileRepository;
use Pyrex\DupeBundle\Repository\DupeGroupRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class StatController extends Controller
{
    /**
     * @param $groupId
     * @Route("/stat/overview/{max}", name="stat_overview", defaults={"max" = 1})
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function overviewAction($max)
    {
        /** @var Registry $doctrine */
        $doctrine = $this->get('doctrine');
        /** @var EntityManager $entityManager */
        $entityManager = $doctrine->getManager('pyrex_dupe');
        /** @var DupeGroupRepository $dupeGroupRepo */
        $dupeGroupRepo = $entityManager->getRepository('PyrexDupeBundle:DupeGroup');
        /** @var DupeFileRepository $dupeFileRepo */
        $dupeFileRepo  = $entityManager->getRepository('PyrexDupeBundle:DupeFile');
        $groupCount    = $dupeGroupRepo->count();
        $dupeFileCount = $dupeFileRepo->count();

        return $this->render(
            'PyrexDupeBundle:Stat:overview.html.twig',
            array(
                'groupCount'       => $groupCount,
                'dupeFileCount'    => $dupeFileCount,
                'fileDeletedCount' => $dupeFileRepo->deletedCount(),
            )
        );
    }
}
