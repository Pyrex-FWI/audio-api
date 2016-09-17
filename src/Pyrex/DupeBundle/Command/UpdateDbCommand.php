p<?php

namespace Pyrex\DupeBundle\Command;

use Pyrex\DupeBundle\DatabaseImport;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class UpdateDbCommand extends AbstractCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('dupe:update:db')->setDescription('Update Database dupe')
            //->addArgument('provider', InputArgument::REQUIRED, 'Provider (like avd or ddp')
            ->setHelp(<<<EOF
<info>%command.name%</info>
php app/console deejay:pool:status av_district
EOF
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->init($input, $output);

        /** @var DatabaseImport $dbi */
        $dbi = $this->getContainer()->get('pyrex_dupe.database_import');
        $dbi->read();

        return 1;
    }
}
