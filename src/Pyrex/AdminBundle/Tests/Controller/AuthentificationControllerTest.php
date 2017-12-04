<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pyrex\AdminBundle\Tests\Controller;


use Pyrex\AdminBundle\Tests\ClientWebTestTrait;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class AuthentificationControllerTest
 *
 * @package Pyrex\AdminBundle\Tests\Controller
 * @group functional
 */
class AuthentificationControllerTest extends WebTestCase
{
    const PYREX_LOGIN = 'DeejayPyrex';

    use ClientWebTestTrait;

    /**
     * @dataProvider getLocales
     */
    public function testJeMeConnecteAvecMonIdentifiantEtMonMotDePasse($login, $password, $locale)
    {
        $this->submitLoginForm($login, $password, $this->client, $locale);

        $this->assertTrue($this->client->getResponse()->isRedirect());

        $text = $this->client->followRedirect()->text();
        $this->assertContains('Bienvenue '.$login, $text);
    }

    public function testJeMeConnecteAvecUnIdentifiantOuUnMotDePasseIncorrect()
    {
        $this->submitLoginForm(self::PYREX_LOGIN, 'wrong', $this->client);
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $text = $this->client->followRedirect()->text();

        $this->assertContains('Identifiants invalides.', $text);
    }

    public function getLocales()
    {
        return [
            '(fr_deejaypyrex)'    =>  [self::PYREX_LOGIN, 'test', 'fr'],
            '(en_deejaypyrex)'    =>  [self::PYREX_LOGIN, 'test', 'en'],
        ];
    }

    /**
     * @param $login
     * @param $password
     * @param $locale
     * @param $client
     * @return Client
     */
    protected function submitLoginForm($login, $password, Client $client, $locale = 'fr')
    {
        $form = $client->request('GET', sprintf('/%s/login', $locale))->selectButton('login')->form();
        $form['_username'] = $login;
        $form['_password'] = $password;

        return $client->submit($form);
    }
}
