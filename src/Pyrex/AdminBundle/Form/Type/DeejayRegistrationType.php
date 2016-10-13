<?php

namespace Pyrex\AdminBundle\Form\Type;

use Pyrex\CoreModelBundle\Entity\Deejay;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class DeejayRegistrationType
 * @author Christophe Pyree <christophe.pyree@gmail.com>
 * @package AppBundle\Form\Type
 */
class DeejayRegistrationType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add(
                'email',
                EmailType::class,
                []
            )
            ->add(
                'password',
                RepeatedType::class,
                [
                    'type'              => PasswordType::class,
                    'required'          => true,
                    'invalid_message'   => 'The password fields must match.',
                    'first_options'     => ['label' => 'Password'],
                    'second_options'    => ['label' => 'Repeat password'],
                ]
            )
            ->add(
                'submit',
                SubmitType::class
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class'    => Deejay::class
            ]
        );
    }


}

