<?php

namespace AppBundle\EventListener;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
    /** @var array */
    private $availableLanguages = [];
    /** @var string */
    private $defaultLocale;

    /**
     * ApiEventSubscriber constructor.
     *
     * @param Router $router
     * @param array  $availableLanguages
     * @param string $defaultLocale
     */
    public function __construct(Router $router, $availableLanguages, $defaultLocale)
    {
        $this->router = $router;
        $this->routeCollection = $router->getRouteCollection();
        $this->availableLanguages = $availableLanguages;
        $this->defaultLocale = $defaultLocale;
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
     *
     * @return bool
     */
    private function isSupported($locale)
    {
        return in_array($locale, $this->availableLanguages) ? true : false;
    }
}
