<?php
/**
 * User: chpyr
 * Date: 04/09/15
 * Time: 20:09.
 */

namespace AppBundle\Faker\Provider;

use Faker\Generator;

class AvItemProvider extends MediaProvider
{
    public function extension()
    {
        return 'mp3';
    }

    public function avdFileName($identifierRange = null)
    {
        $result = $this->generator->parse('{{id}}_{{artist}}-{{title}} ({{version}}).{{extension}}');

        return $result;
    }
}
