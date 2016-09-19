<?php

namespace Pyrex\DupeBundle;

use Pyrex\DupeBundle\Entity\DupeGroup;
use Symfony\Component\EventDispatcher\Event;

class DupeGroupEvent extends Event
{
    /** @var  DupeGroup */
    private $dupeGroup;

    public function __construct(DupeGroup $dupeGroup)
    {
        $this->dupeGroup = $dupeGroup;
    }

    public function getDupeGroup()
    {
        return $this->dupeGroup;
    }
}
