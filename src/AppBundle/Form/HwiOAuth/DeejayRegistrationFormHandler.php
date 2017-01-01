<?php

namespace AppBundle\Form\HwiOAuth;

use HWI\Bundle\OAuthBundle\Form\RegistrationFormHandlerInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

class DeejayRegistrationFormHandler implements RegistrationFormHandlerInterface
{
    /**
     * Processes the form for a given request.
     *
     * @param Request               $request         Active request
     * @param Form                  $form            Form to process
     * @param UserResponseInterface $userInformation OAuth response
     *
     * @return bool True if the processing was successful
     */
    public function process(Request $request, Form $form, UserResponseInterface $userInformation)
    {
        // TODO: Implement process() method.
    }
}
