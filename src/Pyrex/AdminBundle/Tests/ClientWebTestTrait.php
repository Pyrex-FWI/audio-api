<?php

namespace Pyrex\AdminBundle\Tests;



use Symfony\Bundle\FrameworkBundle\Client;

trait ClientWebTestTrait
{
    /** @var  Client */
    private $client;

    protected function setUp()
    {
        $this->client = $this->createClient();
    }

    /**
     * @return object|\Symfony\Bundle\FrameworkBundle\Routing\Router
     */
    protected function router()
    {
        return $this->client->getContainer()->get('router');
    }

    /**
     * @return null|\Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected function getContainer()
    {
        return $this->client->getContainer();
    }
}
