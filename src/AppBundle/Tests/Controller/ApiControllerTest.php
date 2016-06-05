<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client  = $this->createAuthenticatedClient();
        $crawler = $client->request('GET', '/api/');
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/ld+json'));
        $result = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('@context', $result);
        $this->assertArrayHasKey('@id', $result);
        $this->assertArrayHasKey('@type', $result);
        $this->assertArrayHasKey('media', $result);
    }

    /**
     * Create a client with a default Authorization header.
     *
     * @param string $username
     * @param string $password
     *
     * @return \Symfony\Bundle\FrameworkBundle\Client
     */
    protected function createAuthenticatedClient($username = 'admin', $password = 'adminpass')
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/login_check',
            array(
                '_username' => $username,
                '_password' => $password,
            )
        );

        $data = json_decode($client->getResponse()->getContent(), true);

        $client = static::createClient();
        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $data['token']));

        return $client;
    }
}
