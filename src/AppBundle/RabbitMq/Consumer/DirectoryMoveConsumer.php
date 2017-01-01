<?php

namespace AppBundle\RabbitMq\Consumer;

use AppBundle\Command\TempDirOrganizeCommand;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DirectoryMoveConsumer implements ConsumerInterface
{
    use ContainerAwareTrait;

    /**
     * @var LoggerInterface|NullLogger
     */
    private $logger;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    public function __construct(LoggerInterface $logger, EventDispatcherInterface $eventDispatcher)
    {
        $this->logger = $logger;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param AMQPMessage $msg The message
     *
     * @return mixed false to reject and requeue, any other value to acknowledge
     */
    public function execute(AMQPMessage $msg)
    {
        $kernel = $this->container->get('kernel');
        $application = new \Symfony\Bundle\FrameworkBundle\Console\Application($kernel);
        $application->setAutoExit(false);
        $fs = new \SplFileInfo($msg->body);

        $input = new ArrayInput(array(
            'command' => TempDirOrganizeCommand::NAME,
            'temp-dir' => $msg->body,
            'root-output' => $this->container->getParameter('organize.temp.root_output'),
            //'-vvv'          => null,
            //'--dry-run'     => null
        ));
        // You can use NullOutput() if you don't need the output
        $output = new BufferedOutput();
        $exitCode = false;
        $exitCode = $application->run($input, $output);

        $content = $output->fetch();
        echo date('d/m/y H:i:s').' - '.$content;
        if (!$exitCode) {
            $this->logger->info(sprintf('%s has been moved correctly to %s', $msg->body, $this->container->getParameter('organize.temp.root_output')));
        }

        return !$exitCode;
    }
}
