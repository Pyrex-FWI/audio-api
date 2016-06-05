<?php

namespace AppBundle\Organizer\Rules;



use AppBundle\Organizer\MediaMoveStack;

class MediaReleaseMonthRule extends FileMonthRule
{

    public function apply(MediaMoveStack $mover)
    {
        $part = null;
        $date = null;
        if ($mover->getMedia()->getReleaseDate()) {
            $date = $mover->getMedia()->getReleaseDate()->format('F');
        }
        return $date ? $mover->addPathPart($date) : parent::apply($mover);
    }

    public function getName()
    {
        return 'media_month';
    }
}