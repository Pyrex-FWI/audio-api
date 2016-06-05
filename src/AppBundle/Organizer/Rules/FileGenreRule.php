<?php

namespace AppBundle\Organizer\Rules;

use AppBundle\Organizer\MediaMoveStack;

class FileGenreRule implements RuleInterface
{
    public function apply(MediaMoveStack $mover)
    {
        if ($mover->getTagInfo()->getGenres()) {
            $mover->addPathPart($mover->getTagInfo()->getGenres());
        }
    }

    public function getName()
    {
        return 'genre';
    }
}
