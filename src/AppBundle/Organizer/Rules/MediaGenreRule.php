<?php

namespace AppBundle\Organizer\Rules;

use AppBundle\Organizer\MediaMoveStack;

class MediaGenreRule extends FileGenreRule
{
    public function apply(MediaMoveStack $mover)
    {
        $part = null;
        if ($mover->getMedia()->getGenres()->count() > 0) {
            $mover->addPathPart($mover->getMedia()->getGenres()->get(0)->getName());
        } else {
            parent::apply($mover);
        }
    }

    public function getName()
    {
        return 'media_genre';
    }
}
