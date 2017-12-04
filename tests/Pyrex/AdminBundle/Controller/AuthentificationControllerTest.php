<?php

namespace Tests\AppBundle\Controller;

use Pyrex\CoreModelBundle\Entity\Deejay;
use Tests\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

class AuthentificationControllerTest extends WebTestCase
{
    /**
     * @dataProvider loginPasswordUserProvider
     */
    public function testLogin($login, $password, $excepectedMessage)
    {
        $client = static::createClient();
        $client->followRedirects();

        $this->login($client, $login, $password);

        $this->assertTrue($client->getResponse()->isRedirection() || $client->getResponse()->isSuccessful());
        $this->assertContains($excepectedMessage, $client->getResponse()->getContent());
    }

    public function testRegister()
    {
        $client = static::createClient();
        $client->followRedirects();
        $name = 'Monsieur Test';
        $password = 'pass1234';
        $email = 'fake@gmail.com';

        $nbStart = $client->getContainer()->get('doctrine')->getManager()->getRepository(Deejay::class)->count();
        $crawler = $this->doRequest($client, 'register');
        $formData['deejay_registration[name]'] = $name;
        $form = $crawler->selectButton('Submit')->form($formData);
        $crawler = $client->submit($form);
        $text = $crawler->filter('.user-registration')->text();
        $this->assertContains('Cette valeur ne doit pas être vide', $text);
        $formData['deejay_registration[email]'] = 'WrongEmail';
        $form = $crawler->selectButton('Submit')->form($formData);
        $crawler = $client->submit($form);
        $text = $crawler->filter('.user-registration')->text();
        $this->assertContains('Cette valeur n\'est pas une adresse email valide', $text);
        $formData['deejay_registration[email]'] = $email;
        $formData['deejay_registration[password][first]'] = $password;
        $formData['deejay_registration[password][second]'] = $password.'_FAKE';
        $form = $crawler->selectButton('Submit')->form($formData);
        $crawler = $client->submit($form);
        $text = $crawler->filter('.user-registration')->text();
        $this->assertContains('The password fields must match.Repeat', $text);
        $formData['deejay_registration[password][second]'] = 'pass1234';
        $form = $crawler->selectButton('Submit')->form($formData);
        $crawler = $client->submit($form);

        $nbAfter = $client->getContainer()->get('doctrine')->getManager()->getRepository(Deejay::class)->count();
        $this->assertEquals($nbAfter, $nbStart+1);
        /** @var Deejay $new */
        $new = $client->getContainer()->get('doctrine')->getManager()->getRepository(Deejay::class)->findOneByEmail($email);
        dump($new);
        $this->assertEquals($name, $new->getName());
        $this->assertInstanceOf(\DateTime::class, $new->getCreatedAt());
        $this->assertInstanceOf(\DateTime::class, $new->getUpdatedAt());
        $this->assertFalse($new->getEnabled());
        $this->assertTrue(password_verify($password, $new->getPassword()));
//        $client->getContainer()->get('doctrine')->getManager()->remove($new);
//        $client->getContainer()->get('doctrine')->getManager()->flush();
    }

    public function testActivation()
    {
        $client = static::createClient();

    }
    public function loginPasswordUserProvider()
    {
        return [
            ['yemistikris@hotmail.fr', 'test', 'Bienvenue DeejayPyrex'],
            ['yemistikris@hotmail.fr', 'xxx', 'Identifiants invalides.']
        ];
    }

    /**
     * @param $client
     * @param $loginOrEmail
     * @param $password
     * @return Crawler
     */
    protected function login($client, $loginOrEmail, $password)
    {
        $crawler = $this->doRequest($client,'login');

        $loginForm = $crawler->selectButton("valider")->form(
            [
                '_username' => $loginOrEmail,
                '_password' => $password,
            ]
        );

        return $client->submit($loginForm);
    }
}
