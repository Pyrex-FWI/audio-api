<?php

namespace Pyrex\DupeBundle\EventListener;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Monolog\Logger;
use Pyrex\DupeBundle\DupeGroupEvent;
use Pyrex\DupeBundle\Entity\DupeFile;
use Pyrex\DupeBundle\Entity\DupeGroup;
use Pyrex\DupeBundle\Repository\DupeFileRepository;

/**
 * DumpEventListener.
 */
class DumpEventListener
{
    /**
     * @var EntityManager
     */
    private $entityManager;
    private $logger;

    public function __construct(EntityManager $entityManager, Logger $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger        = $logger;
        $this->entityManager->getConnection()->getConfiguration()->setSQLLogger(null);
    }

    public function onDumpReadDuplicateGroup(DupeGroupEvent $dupeGroupeEvent)
    {
        $dupeGroup = $dupeGroupeEvent->getDupeGroup();
        if (!$this->alreadySet($dupeGroup)) {
            $this->entityManager->persist($dupeGroup);
            $this->entityManager->flush();
            $this->entityManager->clear();
            gc_collect_cycles();
        }
    }

    private function alreadySet(DupeGroup $dupeGroup)
    {
        /** @var ArrayCollection $dupeFiles */
        $dupeFiles   = $dupeGroup->getDupeFiles();
        $dupeFileIds = [];
        foreach ($dupeFiles as $dupeFile) {
            /* @var DupeFile $dupeFile */
            $dupeFileIds[] = $dupeFile->getId();
        }
        /* @var DupeFile $dupeFile */
        /** @var DupeFileRepository $dupeFileRepository */
        $dupeFileRepository = $this->entityManager->getRepository(DupeFile::class);

        $filesFound = $dupeFileRepository->findBy(['id' => $dupeFileIds]);

        if (count($filesFound) == $dupeFiles->count()) {
            $this->logger->info('Group and file already tracked', $filesFound);

            return true;
        }

        if (count($filesFound) > 0) {
            foreach ($filesFound as $file) {
                $dupeGroup->removeFile($file);
            }
        }
    }
}
