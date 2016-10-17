<?php
/**
 * Copyright (c) 2016. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
 * Morbi non lorem porttitor neque feugiat blandit. Ut vitae ipsum eget quam lacinia accumsan.
 * Etiam sed turpis ac ipsum condimentum fringilla. Maecenas magna.
 * Proin dapibus sapien vel ante. Aliquam erat volutpat. Pellentesque sagittis ligula eget metus.
 * Vestibulum commodo. Ut rhoncus gravida arcu.
 */

namespace AppBundle\Twig;

use Pyrex\CoreModelBundle\Entity\Media;

/**
 * Class AppExtention
 * @author Christophe Pyree <christophe.pyree@gmail.com>
 * @package AppBundle\Twig
 */
class AppExtention extends \Twig_Extension
{
    /**
     * @return array
     */
    public function getTests()
    {
        return [
            new \Twig_SimpleTest('audio', [$this, 'isAudioFile']),
            new \Twig_SimpleTest('video', [$this, 'isVideoFile']),
        ];
    }

    /**
     * @param Media $media
     * @return bool
     */
    public function isAudioFile(Media $media)
    {
        $parts = pathinfo($media->getFileName());

        return in_array(strtolower($parts['extension']), ['mp3', 'flac']) ? true : false;
    }

    /**
     * @param Media $media
     * @return bool
     */
    public function isVideoFile(Media $media)
    {
        $parts = pathinfo($media->getFileName());

        return in_array(strtolower($parts['extension']), ['mp4']) ? true : false;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'app_extension';
    }

}