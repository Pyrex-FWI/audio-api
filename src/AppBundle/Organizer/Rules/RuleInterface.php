<?php

namespace AppBundle\Organizer\Rules;

use AppBundle\Organizer\MediaMoveStack;

interface RuleInterface
{
    public function apply(MediaMoveStack $mover);

    public function getName();
}
