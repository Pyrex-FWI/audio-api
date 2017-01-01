<?php

namespace Pyrex\DupeBundle\Controller;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Pyrex\DupeBundle\Entity\DupeFile;
use Pyrex\DupeBundle\Repository\DupeFileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class ScriptController extends Controller
{
    /**
     * @param $groupId
     * @Route("/script/remove", name="rm_script")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function consoleRmAction()
    {
        /** @var Registry $doctrine */
        $doctrine = $this->get('doctrine');
        /** @var EntityManager $entityManager */
        $entityManager = $doctrine->getManager('pyrex_dupe');
        /** @var DupeFileRepository $dupeFileRepo */
        $dupeFileRepo = $entityManager->getRepository('PyrexDupeBundle:DupeFile');
        $dupeFiles = $dupeFileRepo->findBy(['deleteFlag' => true]);
        $cmd = [];
        foreach ($dupeFiles as $file) {
            /* @var DupeFile $file */
            $cmd[] = sprintf('rm "%s"', $file->getPathFile());
        }

        return new Response(implode('<br/>', $cmd));
    }
}
