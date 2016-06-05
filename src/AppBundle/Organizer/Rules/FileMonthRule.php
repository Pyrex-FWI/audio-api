<?php

namespace AppBundle\Organizer\Rules;

class FileMonthRule implements RuleInterface
{
    public function apply(\AppBundle\Organizer\MediaMoveStack $mover)
    {
        $newPart = (new \DateTime('@'.$mover->getFsys()->getCTime()))->format('F');
        $mover->addPathPart($newPart);
    }

    public function getName()
    {
        return 'created_month';
    }
}
