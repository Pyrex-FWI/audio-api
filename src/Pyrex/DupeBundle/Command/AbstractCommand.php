<?php

namespace Pyrex\DupeBundle\Command;

use DeejayPoolBundle\Provider\PoolProviderInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

abstract class AbstractCommand extends ContainerAwareCommand
{
    /** @var  \DeejayPoolBundle\Provider\ProviderManager */
    protected $manager;

    /** @var  PoolProviderInterface */
    protected $provider;

    /** @var InputInterface */
    protected $input;

    /**     * @var OutputInterface */
    protected $output;

    /** @var  ProgressBar */
    protected $progressBar;

    /** @var EventDispatcher */
    protected $eventDispatcher;

    /** @var Logger; */
    protected $logger;

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    public function init(InputInterface $input, OutputInterface $output)
    {
        $this->input  = $input;
        $this->output = $output;
    }

    public function initProgressBar($max)
    {
        $this->progressBar = new ProgressBar($this->output, $max);
        ProgressBar::setFormatDefinition(
            'debug', "%message%\n%current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%");
        $this->progressBar->setFormat('debug');
    }
}
