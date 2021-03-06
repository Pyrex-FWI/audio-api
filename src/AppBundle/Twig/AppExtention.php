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
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Route;

/**
 * Class AppExtention
 * @author Christophe Pyree <christophe.pyree@gmail.com>
 * @package AppBundle\Twig
 */
class AppExtention extends \Twig_Extension
{
    /** @var RequestStack $ requestStack */
    private $requestStack;
    private $allowed_directories;
    /** @var Router  */
    private $router;

    public function __construct(RequestStack $requestStack, Router $router, $allowed_directories)
    {
        $this->requestStack = $requestStack;
        $this->allowed_directories = $allowed_directories;
        $this->router = $router;
    }
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
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('left_menu_class', array($this, 'activeLinkClass')),
            new \Twig_SimpleFunction('media_icon_class', array($this, 'getMediaIconClass')),
            new \Twig_SimpleFunction('amplitude_songs', array($this, 'transformMediaListToJsonAmplitude')),
        ];
    }

    /**
     * @param Media[] $medias
     */
    public function transformMediaListToJsonAmplitude($medias)
    {

        $data = array_map(
            function (Media $item) {
                return [
                    'name'          => $item->getTitle(),
                    'album'         => '',
                    'artist'        => $item->getArtist(),
                    'url'           => $this->router->getGenerator()->generate('stream', ['file' => $this->allowed_directories[0].DIRECTORY_SEPARATOR.'dir01/01.mp3']),
                    'cover_art_url' => null,
                ];
            },
            (array) $medias
        );

        return json_encode($data);

    }
    /**
     * Return nice class name for a media
     * @param  Media  $media      [description]
     * @param  string $audioClass [description]
     * @param  string $viedoClass [description]
     * @return [type]             [description]
     */
    public function getMediaIconClass(Media $media, $audioClass = 'glyphicon glyphicon-music', $videoClass = 'glyphicon glyphicon-facetime-video')
    {
        if ($this->isAudioFile($media)) {
            return $audioClass;
        }
        if ($this->isVideoFile($media)) {
            return $videoClass;
        }
    }
    /**
     * Return nice class name if given route equal $routeName arg
     * @param  string $routeName         [description]
     * @param  string $activeClassName   [description]
     * @param  string $inactiveClassName [description]
     * @return string                    className to use
     */
    public function activeLinkClass($routeName, $activeClassName = 'active', $inactiveClassName = "")
    {
        if ($this->requestStack->getCurrentRequest()->attributes->get('_route') === $routeName) {
            return $activeClassName;
        }

        return $inactiveClassName;
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
