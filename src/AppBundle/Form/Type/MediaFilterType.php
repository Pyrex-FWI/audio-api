<?php
/**
 * Copyright (c) 2016. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
 * Morbi non lorem porttitor neque feugiat blandit. Ut vitae ipsum eget quam lacinia accumsan.
 * Etiam sed turpis ac ipsum condimentum fringilla. Maecenas magna.
 * Proin dapibus sapien vel ante. Aliquam erat volutpat. Pellentesque sagittis ligula eget metus.
 * Vestibulum commodo. Ut rhoncus gravida arcu.
 */

namespace AppBundle\Form\Type;

use Lexik\Bundle\FormFilterBundle\Filter\FilterOperands;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type\EntityFilterType;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type\NumberFilterType;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type\TextFilterType;
use Pyrex\CoreModelBundle\Entity\Genre;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class MediaFilterType.
 *
 * @author Christophe Pyree <christophe.pyree@gmail.com>
 */
class MediaFilterType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     *
     * @return FormBuilderInterface
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'title',
                TextFilterType::class,
                ['condition_pattern' => FilterOperands::STRING_CONTAINS]
            )
            ->add(
                'artist',
                TextFilterType::class,
                ['condition_pattern' => FilterOperands::STRING_CONTAINS]
            )
            ->add(
                'genres',
                EntityFilterType::class,
                [
                    'class' => Genre::class,
                    'choice_label' => 'name',
                    'multiple' => true,
                ]
            )
            ->add(
                'bpm',
                NoUiSliderFilterType::class,
                [
                    'range_min' => 60,
                    'range_max' => 180,
                ]
            )
            ->add(
                'year',
                NumberFilterType::class
            )
            ->add('filter', SubmitType::class)
            ;

        return $builder;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => false,
            'validation_groups' => array('filtering'), // avoid NotBlank() constraint-related message,
            'label_format' => 'media_filter.%name%',
            'translation_domain' => 'forms',
        ));
    }
}
