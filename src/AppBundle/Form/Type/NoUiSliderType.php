<?php
/**
 * Copyright (c) 2016. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
 * Morbi non lorem porttitor neque feugiat blandit. Ut vitae ipsum eget quam lacinia accumsan.
 * Etiam sed turpis ac ipsum condimentum fringilla. Maecenas magna.
 * Proin dapibus sapien vel ante. Aliquam erat volutpat. Pellentesque sagittis ligula eget metus.
 * Vestibulum commodo. Ut rhoncus gravida arcu.
 */

namespace AppBundle\Form\Type;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class NoUiSliderType
 * @author Christophe Pyree <christophe.pyree@gmail.com>
 * @package AppBundle\Form\Type
 */
class NoUiSliderType extends AbstractType
{
    private $twig;
    private $eventDispatcher;

    /**
     * NoUiSliderType constructor.
     * @param \Twig_Environment $twig
     * @param $eventDispatcher
     */
    public function __construct(\Twig_Environment $twig, EventDispatcherInterface $eventDispatcher)
    {
        $this->twig             = $twig;
        $this->eventDispatcher  = $eventDispatcher;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('min', HiddenType::class)
            ->add('max', HiddenType::class);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'compound'  => true,
            'start_min' => 0,
            'start_max' => 100,
            'range_min' => 0,
            'range_max' => 100,
            'step'      => 1,
        ]);
    }

    /**
     * @return mixed
     */
    public function getParent()
    {
        return TextType::class;
    }

    /**
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['range_min'] = $options['range_min'];
        $view->vars['range_max'] = $options['range_max'];
        $view->vars['start_min'] = $options['start_min'];
        $view->vars['start_max'] = $options['start_max'];
        $view->vars['step']      = $options['step'];

        $javascriptContent       = $this->twig->render('AppBundle:Form:NoUiSlider.js.twig', $view->vars);
        $view->vars['js']        = $javascriptContent;
        /*$this->eventDispatcher->addListener('kernel.response', function($event) use ($javascriptContent) {

            $response = $event->getResponse();
            $content = $response->getContent();
            // finding position of </body> tag to add content before the end of the tag
            $pos = strripos($content, '</body>');
            $content = substr($content, 0, $pos).$javascriptContent.substr($content, $pos);

            $response->setContent($content);
            $event->setResponse($response);
        });
        */
    }


}
