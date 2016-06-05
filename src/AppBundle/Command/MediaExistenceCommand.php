<?php

/*
 * This file is part of the Audio Api.
 *
 * (c) Christophe Pyree
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Command;

use AppBundle\Entity\Media;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 *
 */
class MediaExistenceCommand extends ContainerAwareCommand
{
    private $exist    = 0;
    private $notExist = 0;
    private $total    = 0;
    private $output;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('media:update:existence')
            ->setDescription('Check if media referenced into database exist')
                  ->setHelp(<<<EOF
The <info>%command.name%</info> command greets somebody or everybody:

<info>php %command.full_name%</info>

The optional argument specifies who to greet:

<info>php %command.full_name%</info> Fabien
EOF
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var Registry $doctrine */
        $doctrine     = $this->getContainer()->get('doctrine');
        $this->output = $output;
        /** @var EntityRepository $mediaRepository */
        $mediaRepository = $doctrine->getRepository('\AppBundle\Entity\Media');
        /** @var EntityManager $em */
        $em = $doctrine->getManager();
        $em->getConnection()->getConfiguration()->setSQLLogger(null);
        $query       = $em->createQuery('SELECT COUNT(m.id) FROM \AppBundle\Entity\Media m');
        $this->total = $query->getSingleScalarResult();
        $from        = 0;
        $size        = 1000;

        $queryBuilder = $mediaRepository->createQueryBuilder('m');
        $queryBuilder->select('m');
        $queryBuilder->setFirstResult($from);
        $queryBuilder->setMaxResults($size);
        $this->progressBar = new ProgressBar($output, $this->total);
        ProgressBar::setFormatDefinition(
            'debug',
            "\n%current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%");
        $this->progressBar->setFormat('debug');

        while ($from < $this->total + $size) {
            foreach ($queryBuilder->getQuery()->getResult() as $media) {
                /* @var Media $media */
                $changed = false;
                if (file_exists($media->getFullPath())) {
                    $media->setExist(true);
                    ++$this->exist;

                    if (!$media->getExist()) {
                        $changed = true;
                    }
                } else {
                    $media->setExist(false);
                    ++$this->notExist;
                    if ($media->getExist()) {
                        $changed = true;
                    }
                }
                if ($changed) {
                    $doctrine->getManager()->persist($media);
                }
                $this->progressBar->advance();
            }
            $doctrine->getManager()->flush();
            $doctrine->getManager()->clear();
            $from += $size;
            $queryBuilder->setFirstResult($from);
        }

        $this->progressBar->finish();

        $output->writeln('');

        $this->printSummary();
    }

    private function printSummary()
    {
        $this->output->writeln('<info>Summary</info>');

        $tableHelper = new Table($this->output);
        $tableHelper->addRow(['Total', $this->total]);
        $tableHelper->addRow(['Exist', ($this->exist)]);
        $tableHelper->addRow(['Not exist', ($this->notExist)]);
        $tableHelper->render();
    }
}
