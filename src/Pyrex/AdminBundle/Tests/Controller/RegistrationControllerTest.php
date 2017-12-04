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
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class RegistrationControllerTest
 *
 * @package Pyrex\AdminBundle\Tests\Controller
 * @group functional
 */
class RegistrationControllerTest extends WebTestCase
{
    const PYREX_LOGIN = 'DeejayPyrex';

    use ClientWebTestTrait;

    private function removeDeejay($name)
    {
        if ($alreadyExist = $this->getContainer()->get('repository.deejay')->findOneByName($name)) {
            $this->getContainer()->get('doctrine.orm.default_entity_manager')->remove($alreadyExist);
            $this->getContainer()->get('doctrine.orm.default_entity_manager')->flush($alreadyExist);
        }
    }

    public function testJeMeCreeUnCompteEtJesuisRedirigeSurUnePageDeConfirmationEnCasDeSucces()
    {
        $name = 'PhpunitName';

        $this->removeDeejay($name);

        $form = $this->client->request('GET', $this->router()->generate('register'))->selectButton('Submit')->form();
        $form['deejay_registration[name]'] = $name;
        $form['deejay_registration[email]'] = 'yemistikrys@gmail.com';
        $form['deejay_registration[password][second]'] = 'SomeName';
        $form['deejay_registration[password][first]'] = 'SomeName';
        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect());
        $resultCrawler = $this->client->followRedirect();

        $this->assertTrue(
            $resultCrawler->filter(
                sprintf(
                    'div.flash-succes:contains("%s")',
                    $this->getContainer()->get('translator')->trans('deejay_registration_type.success_flash_message', [], 'flash'))
            )->count() == 1
        );

        return [$resultCrawler, $name];
    }

    /**
     * @depends testJeMeCreeUnCompteEtJesuisRedirigeSurUnePageDeConfirmationEnCasDeSucces
     */
    public function testApresLaCreationDuCompteJeSuisCapableDeDeclencherLenvoiDunNouvelEmailPourDactivation($dependsArgs)
    {
        /** @var Crawler $crawler */
        $crawler = $dependsArgs[0];
        $name = $dependsArgs[1];
        dump($crawler->text());
        $link = $crawler->selectLink('confirmationResendLink')->link()->getUri();
        dump($link);
        $this->client->request('GET', $link);
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $resultCrawler = $this->client->followRedirect();

        $this->removeDeejay($name);
    }
}
