<?php

use Symfony\Bundle\FrameworkBundle\HttpCache\HttpCache;
use Symfony\Component\HttpFoundation\Response;
/**
 * Class AppCache
 * @author Christophe Pyree <christophe.pyree@gmail.com>
 */
class AppCache extends HttpCache
{
    protected function invalidate(\Symfony\Component\HttpFoundation\Request $request, $catch = false)
    {
        if ('PURGE' !== $request->getMethod()) {
            return parent::invalidate($request, $catch);
        }

        /*if ('127.0.0.1' !== $request->getClientIp()) {
            return new Response(
                'Invalid HTTP method',
                \Symfony\Component\HttpFoundation\Response::HTTP_BAD_REQUEST
            );
        }*/

        $response = new \Symfony\Component\HttpFoundation\Response();
        if ($this->getStore()->purge($request->getUri())) {
            $response->setStatusCode(200, 'Purged');
        } else {
            $response->setStatusCode(404, 'Not found');
        }

        return $response;
    }

}
