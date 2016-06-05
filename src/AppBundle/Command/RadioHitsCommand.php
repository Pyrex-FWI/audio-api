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

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityRepository;
use RadioHitsBundle\Radio\RadioManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 *
 */
class RadioHitsCommand extends ContainerAwareCommand
{
    /** @var  OutputInterface */
    private $output;
    /** @var  InputInterface */
    private $input;
    /** @var  Registry */
    private $doctrine;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('radio:hits:fetch')
            ->setDescription('Retreive radio hits')
                  ->setHelp(<<<EOF
The <info>%command.name%</info>
<info>php %command.full_name%</info>


<info>php %command.full_name%</info>
EOF
            );
    }

    private function init(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->input = $input;
        $this->doctrine = $this->getContainer()->get('doctrine');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->init($input, $output);
        /** @var RadioManager $radioManager */
        $radioManager = $this->getContainer()->get('radio_manager');
        /* @var EntityRepository $mediaRepository */
        //$em->getConnection()->getConfiguration()->setSQLLogger(null);

        foreach ($radioManager->getRadioList() as $radio) {
            dump($radio->getName());
            dump($radio->extractHits());
        }
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
