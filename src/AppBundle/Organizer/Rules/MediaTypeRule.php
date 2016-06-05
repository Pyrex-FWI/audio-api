<?php

namespace AppBundle\Organizer\Rules;

use AppBundle\Entity\Media;
use AppBundle\Organizer\MediaMoveStack;

class MediaTypeRule implements RuleInterface
{
    public function apply(MediaMoveStack $mover)
    {
        $part = 'UnknowType';
        if (in_array($mover->getMedia()->getType(), Media::getTypes())) {
            if ($key = array_search($mover->getMedia()->getType(), Media::getTypes())) {
                $part = ucfirst(strtolower($key));
            }
        }
        $mover->addPathPart($part);
    }

    public function getName()
    {
        return 'media_type';
    }
}
