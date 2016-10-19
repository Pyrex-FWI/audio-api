<?php
/**
 * Copyright (c) 2016. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
 * Morbi non lorem porttitor neque feugiat blandit. Ut vitae ipsum eget quam lacinia accumsan.
 * Etiam sed turpis ac ipsum condimentum fringilla. Maecenas magna.
 * Proin dapibus sapien vel ante. Aliquam erat volutpat. Pellentesque sagittis ligula eget metus.
 * Vestibulum commodo. Ut rhoncus gravida arcu.
 */

namespace AppBundle\Form\Type;

use Pyrex\CoreModelBundle\Entity\Deejay;
use Pyrex\CoreModelBundle\Entity\Genre;
use Pyrex\CoreModelBundle\Entity\Media;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class EditMediaType
 * @author Christophe Pyree <christophe.pyree@gmail.com>
 * @package AppBundle\Form\Type
 */
class EditMediaType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', TextType::class, ['disabled' => true])
            ->add('title')
            ->add(
                'artist'
            )
            ->add(
                'bpm'
            )
            ->add(
                'releaseDate'
            )
            ->add(
                'version'
            )
            ->add(
                'exist'
            )
            ->add(
                'tagged'
            )
            ->add(
                'score'
            )
            ->add(
                'genres',
                EntityType::class,
                [
                    'class'         => Genre::class,
                    'choice_label'  => 'name',
                ]
            )
            ->add(
                'artists'
            )
            ->add(
                'type'
            )
            ->add(
                'year'
            )
            ->add('fullPath', TextType::class, ['disabled' => true])
            ->add('fullFilePathMd5', TextType::class, ['disabled' => true])
            ->add('dirName', TextType::class, ['disabled' => true])
            ->add('fileName', TextType::class, ['disabled' => true])
            ->add(
                'Update',
                SubmitType::class
            )
            ->add(
                'Update id3',
                SubmitType::class
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class'    => Media::class
            ]
        );
    }


}
