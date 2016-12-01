<?php
/**
 * Copyright (c) 2016. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
 * Morbi non lorem porttitor neque feugiat blandit. Ut vitae ipsum eget quam lacinia accumsan.
 * Etiam sed turpis ac ipsum condimentum fringilla. Maecenas magna.
 * Proin dapibus sapien vel ante. Aliquam erat volutpat. Pellentesque sagittis ligula eget metus.
 * Vestibulum commodo. Ut rhoncus gravida arcu.
 */

namespace AppBundle\EventListener;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Routing\RouteCollection;

/**
 * LocalListener.
 */
class LocalListener
{
    /**
     * @var Router
     */
    private $router;
    /**
     * @var RouteCollection
     */
    private $routeCollection;
    private $availableLanguages = [];
    private $defaultLocale;

    /**
     * ApiEventSubscriber constructor.
     * @param Router     $router
     * @param array      $availableLanguages
     */
    public function __construct(Router $router, $availableLanguages, $defaultLocale)
    {
        $this->router             = $router;
        $this->routeCollection    = $router->getRouteCollection();
        $this->availableLanguages = $availableLanguages;
        $this->defaultLocale      = $defaultLocale;
    }

    /**
     * @param GetResponseEvent $getResponseEvent
     */
    public function onKernelRequest(GetResponseEvent $getResponseEvent)
    {
        $request = $getResponseEvent->getRequest();
        $requestedPath = $request->getPathInfo();
        $mustRedirect = false;
        foreach ($this->routeCollection->getIterator() as $route) {
            if ($route->getPath() === '/{_locale}'.$requestedPath) {
                $mustRedirect = true;
            }
        }
        if ($mustRedirect) {
            $locale = substr($request->getPreferredLanguage(), 0, 2);
            if (!$locale || !$this->isSupported($locale)) {
                $locale = $request->getDefaultLocale();
            }
            $getResponseEvent->setResponse(new RedirectResponse('/'.$locale.$requestedPath));
        }
    }

    /**
     * @param $locale
     * @return bool
     */
    private function isSupported($locale)
    {
        return in_array($locale, $this->availableLanguages) ? true : false;
    }
}
