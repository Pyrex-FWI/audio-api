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
use AppBundle\Organizer\MediaOrganizerManager;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 *
 */
class MediaOrganizeCommand extends ContainerAwareCommand
{
    private $total = 0;
    private $outputDir;
    private $providerName;
    private $providerId;
    private $rules;
    /** @var  OutputInterface */
    private $output;
    /** @var  InputInterface */
    private $input;
    private $moved = 0;
    private $error = 0;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('organize:media')
            ->setDescription('Reorganize files')
            ->addArgument('providerName', InputArgument::REQUIRED)
            ->addArgument('rules', InputArgument::REQUIRED)
            ->addArgument('outputDir', InputArgument::REQUIRED, 'Dir Output for reorganisazion (must be on same volume)')
            //->addOption('dry', 'd', InputOption::VALUE_NONE, 'Run command in test mode')
            //->addOption('show-providers', 'sp', InputOption::VALUE_NONE, 'show providers list')
            //->addOption('show-rules', 'sr', InputOption::VALUE_NONE, 'show rules list')
            ->setHelp(<<<EOF
The <info>%command.name%</info> reorganize files into outpurDir filesystem:

<info>php %command.full_name%</info>

The argument providerName is mandatory.
Possible values are:
==========================================
{$this->getProvidersString()}
==========================================

The argument rules is mandatory and multiple rules - comma separated - can be provided
like rule1,rule2,rule3
Possible values are:
==========================================
    - genre (file genre)
    - created_month (file ctime)
    - created_year (file ctime)
    - media_genre
    - media_month
    - media_year
    - media_type
==========================================

Example:
<info>php %command.full_name%</info> /var/output/
EOF
            );
    }

    /**
     * @return MediaOrganizerManager
     */
    public function getManager()
    {
        return $this->getContainer()->get('app.media.media_organizer.manager');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->init($input, $output);
        /** @var Registry $doctrine */
        $doctrine = $this->getContainer()->get('doctrine');
        /** @var \AppBundle\Organizer\MediaOrganizerManager $fileOrganizer  */
        $fileOrganizer = $this->getManager();
        /** @var EntityRepository $mediaRepository */
        $mediaRepository = $doctrine->getRepository('\AppBundle\Entity\Media');
        /** @var EntityManager $em */
        $em = $doctrine->getManager();
        $em->getConnection()->getConfiguration()->setSQLLogger(null);

        $query = $em->createQuery('SELECT COUNT(m.id) FROM \AppBundle\Entity\Media m WHERE m.exist = 1 and m.provider = :provider');
        $query->setParameter('provider', $this->providerId);

        $this->total  = $query->getSingleScalarResult();
        $from         = 0;
        $size         = 1000;
        $queryBuilder = $mediaRepository->createQueryBuilder('m');
        $queryBuilder
            ->select('m')
            ->where('m.exist = 1 and m.provider = :provider')
            ->setParameter('provider', $this->providerId)
            ->setFirstResult($from)
            ->setMaxResults($size);

        $this->progressBar = new ProgressBar($output, $this->total);
        ProgressBar::setFormatDefinition(
            'debug',
            "\n%current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%");
        $this->progressBar->setFormat('debug');

        while ($from < $this->total + $size) {
            foreach ($queryBuilder->getQuery()->getResult() as $media) {

                /* @var Media $media */
                $this->progressBar->advance();
                if (!file_exists($media->getFullPath())) {
                    continue;
                }
                $result = $fileOrganizer->apply($this->outputDir, $media, $this->rules);
                if ($result === true) {
                    ++$this->moved;
                    $doctrine->getManager()->persist($media);
                } elseif ($result === false) {
                    ++$this->error;
                }
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

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @throws \Exception
     */
    private function init(InputInterface $input, OutputInterface $output)
    {
        $this->outputDir    = $input->getArgument('outputDir');
        $this->providerName = $input->getArgument('providerName');
        $this->output       = $output;
        $this->input        = $input;

        if (!in_array($this->providerName, array_keys(Media::getProviders()))) {
            throw new \Exception(
                sprintf(
                    " provider '%s' not exist\n available providers are:\n%s",
                    $this->providerName,
                    $this->getProvidersString()
                ));
        }

        foreach (explode(',', $input->getArgument('rules')) as $rule) {
            $rule = trim($rule);
            if (!in_array($rule, array_keys($this->getManager()->getRules()))) {
                throw new \Exception(
                    sprintf(
                        "%s is not available. Available rules are:\n%s",
                        $rule,
                        $this->getRulesString()
                    )
                );
            }
            $this->rules[] = $rule;
        }
        $this->providerId = Media::getProviders()[$this->providerName];
    }

    private function getProvidersString($sep = "\t- ")
    {
        $providers = array_keys(Media::getProviders());
        sort($providers);

        return $sep.implode("\n".$sep, $providers);
    }

    private function getRulesString($sep = "\t- ")
    {
        return $sep.implode("\n".$sep, array_keys($this->getManager()->getRules()));
    }

    /**
     * Print Summary.
     */
    private function printSummary()
    {
        $this->output->writeln('<info>Summary</info>');
        $tableHelper = new Table($this->output);
        $tableHelper->addRow(['Total', $this->total]);
        $tableHelper->addRow(['Error', ($this->error)]);
        $tableHelper->render();
    }
}
