<?php

namespace AppBundle\Organizer\Rules;

use AppBundle\Organizer\MediaMoveStack;

class MediaReleaseYearRule extends FileYearRule
{
    public function apply(MediaMoveStack $mover)
    {
        $part = null;
        if ($mover->getMedia()->getReleaseDate() && $date = $mover->getMedia()->getReleaseDate()->format('Y')) {
            $part = $date;
        } elseif ($id3 = ($mover->getTagInfo())) {
            if (isset($id3['id3v2']['year'][0]) && preg_match('/\d{4}/', $id3['id3v2']['year'][0]) === 1) {
                $part = $id3['id3v2']['year'][0];
            }
        }
        if ($part) {
            $mover->addPathPart($part);
        } else {
            parent::apply($mover);
        }
    }

    public function getName()
    {
        return 'media_year';
    }
}
