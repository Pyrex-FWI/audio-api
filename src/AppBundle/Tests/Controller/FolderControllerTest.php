<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FolderControllerTest extends WebTestCase
{
    /**
     * fetch directory into %allowed_directories%.
     */
    public function testListDirectory()
    {
        $client  = $this->createAuthenticatedClient();
        $crawler = $client->request('GET', '/directory');
        $result  = json_decode($client->getResponse()->getContent(), true);
        $this->assertTrue(count($result) > 0);
        $this->assertArrayHasKey('name', $result[0]);
        $this->assertArrayHasKey('pathName', $result[0]);
        $this->assertArrayHasKey('expanded', $result[0]);
        $this->assertArrayHasKey('childLoaded', $result[0]);
        $this->assertArrayHasKey('isDir', $result[0]);
    }

    /**
     * fetch directory into %allowed_directories%.
     */
    public function testDirectoryContent()
    {
        $client  = $this->createAuthenticatedClient();
        $crawler = $client->request('GET', '/directory');
        $result  = json_decode($client->getResponse()->getContent(), true);
        $path    = $result[0]['pathName'];
        $crawler = $client->request('GET', '/directory/content?path='.$path);
        $result  = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('01.mp3', $result[0]['name']);
        $this->assertEquals($path.'/01.mp3', $result[0]['pathName']);
        $this->assertEquals('02.mp3', $result[1]['name']);
        $this->assertEquals($path.'/02.mp3', $result[1]['pathName']);
        $this->assertEquals('03.mp3', $result[2]['name']);
        $this->assertEquals($path.'/03.mp3', $result[2]['pathName']);
        $this->assertEquals('04.flac', $result[3]['name']);
        $this->assertEquals($path.'/04.flac', $result[3]['pathName']);
    }

    /**
     * @param $name
     *
     * @return string
     */
    public function getDirName($name)
    {
        return realpath(self::$kernel->getContainer()->getParameter('allowed_directories')[0].DIRECTORY_SEPARATOR.$name);
    }
    /**
     * fetch directory into %allowed_directories%.
     */
    public function testDirectoryGetMeta()
    {
        static::bootKernel([]);
        $dir     = ($this->getDirName('dir01'));
        $client  = $this->createAuthenticatedClient();
        $crawler = $client->request('GET', '/directory/get-dir-metadata?path='.$dir);
        $result  = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('file', $result[0]);
        $this->assertArrayHasKey('album', $result[0]);
        $this->assertArrayHasKey('title', $result[0]);
        $this->assertArrayHasKey('artist', $result[0]);
        $this->assertArrayHasKey('artists', $result[0]);
        $this->assertArrayHasKey('genre', $result[0]);
        $this->assertArrayHasKey('comment', $result[0]);
        $this->assertArrayHasKey('year', $result[0]);
        $this->assertArrayHasKey('key', $result[0]);
        $this->assertArrayHasKey('bpm', $result[0]);
        $this->assertArrayHasKey('duration', $result[0]);
    }

    /**
     * @group travis-fail
     */
    public function testDirectorySetMeta()
    {
        static::bootKernel([]);
        $dir     = ($this->getDirName('dir01'));
        $client  = $this->createAuthenticatedClient();
        $year    = (rand(1980, date('Y')));
        $genre   = uniqid();
        $uri     = '/directory/set-dir-metadata?path='.$dir.'&g='.$genre.'&y='.$year;
        $crawler = $client->request('GET', $uri);
        $client->getResponse()->getContent();
        $crawler = $client->request('GET', '/directory/get-dir-metadata?path='.$dir);
        $result  = json_decode($client->getResponse()->getContent(), true);
        foreach ($result as $item) {
            $this->assertEquals($genre, $item['genre']);
            $this->assertEquals($year, $item['year']);
        }
    }

    /**
     * fetch directory into %allowed_directories%.
     */
    public function testListDirectory2()
    {
        $client  = $this->createAuthenticatedClient();
        $crawler = $client->request('GET', '/directory?path='.self::$kernel->getContainer()->getParameter('allowed_directories')[1]);
        $result  = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('Command', $result[0]['name']);
        $this->assertEquals(true, $result[0]['isDir']);
        $this->assertEquals(realpath(self::$kernel->getContainer()->getParameter('allowed_directories')[1].'/Command'), $result[0]['pathName']);
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
