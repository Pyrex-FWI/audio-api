<?php

namespace AppBundle\Service;

use Pyrex\CoreModelBundle\Entity\Deejay;
use Symfony\Bridge\Twig\TwigEngine;

class SystemEmail
{
    /** @var \Swift_Mailer */
    private $mailer;
    /** @var TwigEngine */
    private $twigEngine;

    /**
     * NewRegistrationEventListener constructor.
     *
     * @param \Swift_Mailer $mailer
     * @param TwigEngine    $twigEngine
     */
    public function __construct(\Swift_Mailer $mailer, TwigEngine $twigEngine)
    {
        $this->mailer = $mailer;
        $this->twigEngine = $twigEngine;
    }

    /**
     * @param Deejay $deejay
     */
    public function newRegistrationMail(Deejay $deejay)
    {
        $emailContent = $this->twigEngine->render('PyrexAdminBundle:Authentification:activeUser.html.twig', ['deejay' => $deejay]);
        $message = \Swift_Message::newInstance()
            ->setFrom('yemistikris@hotmail.fr')
            ->setTo('yemistikris@hotmail.fr')
            ->setSubject(sprintf('New deejay (%s) has registered', $deejay->getName()))
            ->setBody(
                $emailContent,
                'text/html'
            );
        $res = $this->mailer->send($message, $failures);
    }
}
