<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\DomCrawler\Crawler;

abstract class WebTestCase extends \Symfony\Bundle\FrameworkBundle\Test\WebTestCase
{

    /**
     * @param Client $client
     * @param $routeName
     * @param string $method
     * @return Crawler
     */
    public function doRequest(Client $client, $routeName, $method = 'GET')
    {
        return $client->request($method, $client->getContainer()->get('router')->generate($routeName));
    }
}
