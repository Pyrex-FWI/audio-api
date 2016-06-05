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
use Doctrine\ORM\Query;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\VarDumper\VarDumper;

/**
 *
 */
class MediaStatCommand extends ContainerAwareCommand
{
    private $exist    = 0;
    private $notExist = 0;
    private $total    = 0;
    /** @var  OutputInterface */
    private $output;
    /** @var  InputInterface */
    private $input;
    /** @var  Registry */
    private $doctrine;

    private $stats = [];
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('media:stats')
            ->setDescription('Build statistic from database')
            ->addOption('provider', null, InputArgument::OPTIONAL, 'Get statistic for specific provider')
                  ->setHelp(<<<EOF
The <info>%command.name%</info> command greets somebody or everybody:

<info>php %command.full_name%</info>


<info>php %command.full_name%</info> Fabien
EOF
            );
    }

    private function init(InputInterface $input, OutputInterface $output)
    {
        $this->output   = $output;
        $this->input    = $input;
        $this->doctrine = $this->getContainer()->get('doctrine');
    }

    /**
     * @param $provider
     *
     * @return mixed
     */
    private function getTotalItems($provider)
    {
        /** @var Query $query */
        $query = $this->doctrine->getManager()->createQuery('SELECT COUNT(m.id) FROM \AppBundle\Entity\Media m where m.provider =  :provider');
        $query->setParameter('provider', $provider);

        return $query->getSingleScalarResult();
    }

    /**
     * @param $provider
     *
     * @return mixed
     */
    private function getItemsWithoutFileName($provider)
    {
        /** @var Query $query */
        $query = $this->doctrine->getManager()->createQuery('SELECT COUNT(m.id) FROM \AppBundle\Entity\Media m where m.provider =  :provider and m.fileName IS NULL');
        $query->setParameter('provider', $provider);

        return $query->getSingleScalarResult();
    }
    /**
     * @param $provider
     *
     * @return mixed
     */
    private function getExitItems($provider)
    {
        /** @var Query $query */
        $query = $this->doctrine->getManager()->createQuery('SELECT COUNT(m.id) FROM \AppBundle\Entity\Media m where m.provider = :provider and m.exist=1');
        $query->setParameter('provider', $provider);

        return $query->getSingleScalarResult();
    }

    /**
     * @param $provider
     *
     * @return mixed
     */
    private function getNoDated($provider)
    {
        /** @var Query $query */
        $query = $this->doctrine->getManager()->createQuery('SELECT COUNT(m.id) FROM \AppBundle\Entity\Media m where m.provider = :provider and m.releaseDate is null');
        $query->setParameter('provider', $provider);

        return $query->getSingleScalarResult();
    }

    /**
     * @param $provider
     *
     * @return mixed
     */
    private function getVersions($provider)
    {
        /** @var Query $query */
        $query = $this->doctrine->getManager()->createQuery('SELECT COUNT(m.version) total, m.version name FROM \AppBundle\Entity\Media m where m.provider = :provider and m.version <> \'\' group by m.version');
        $query->setParameter('provider', $provider);

        return $query->getResult(Query::HYDRATE_ARRAY);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->init($input, $output);
        /** @var EntityRepository $mediaRepository */
        $mediaRepository = $this->doctrine->getRepository('\AppBundle\Entity\Media');
        /* @var EntityManager $em */
        //$em->getConnection()->getConfiguration()->setSQLLogger(null);
        $providers = $this->input->getOption('provider') ? (array) $this->input->getOption('provider') : Media::getProviders();

        foreach ($providers as $provider) {
            $this->stats[$provider]['items']         = $this->getTotalItems($provider);
            $this->stats[$provider]['noFileName']    = $this->getItemsWithoutFileName($provider);
            $this->stats[$provider]['exist']         = $this->getExitItems($provider);
            $this->stats[$provider]['version']       = $this->getVersions($provider);
            $this->stats[$provider]['noReleaseDate'] = $this->getNoDated($provider);
        }
        VarDumper::dump($this->stats);
/*
        $this->progressBar = new ProgressBar($output, $this->total);
        ProgressBar::setFormatDefinition(
            'debug',
            "\n%current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%");
        $this->progressBar->setFormat('debug');

        $this->progressBar->finish();

        $output->writeln("");

        $this->printSummary();
        */
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
