<?php

namespace AppBundle\Organizer\Rules;

class FileYearRule implements RuleInterface
{
    public function apply(\AppBundle\Organizer\MediaMoveStack $mover)
    {
        $newPart = (new \DateTime('@'.$mover->getFsys()->getCTime()))->format('Y');
        $mover->addPathPart($newPart);
    }

    public function getName()
    {
        return 'created_year';
    }
}
