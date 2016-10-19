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
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type\NumberFilterType;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type\NumberRangeFilterType;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type\TextFilterType;
use Lexik\Bundle\FormFilterBundle\Filter\Query\QueryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class MediaFilterType
 * @author Christophe Pyree <christophe.pyree@gmail.com>
 * @package AppBundle\Form\Type
 */
class MediaFilterType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     * @return FormBuilderInterface
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'title',
                TextFilterType::class,
                [ 'condition_pattern' => FilterOperands::STRING_CONTAINS]
            )
            ->add(
                'artist',
                TextFilterType::class,
                [ 'condition_pattern' => FilterOperands::STRING_CONTAINS]
            )
            ->add(
                'year_simple',
                NumberFilterType::class,
                [
                    'apply_filter' => function (QueryInterface $filterQuery, $field, $values) {
                        if (empty($values['value'])) {
                            return null;
                        }
                        $paramName = sprintf('p_%s', str_replace('.', '_', $field));
                        $expression = $filterQuery->getExpr()->eq('e.year', ':'.$paramName);
                        $parameters = array($paramName => $values['value']); // [ name => value ]

                        return $filterQuery->createCondition($expression, $parameters);
                    },
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
                NumberRangeFilterType::class
            )
            ->add('Filter', SubmitType::class)
            ;

        return $builder;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection'   => false,
            'validation_groups' => array('filtering') // avoid NotBlank() constraint-related message
        ));
    }
}
