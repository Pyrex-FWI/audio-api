<?php

namespace Pyrex\AdminBundle\Form\Type;

use Pyrex\CoreModelBundle\Entity\Deejay;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class LoginType
 * @author Christophe Pyree <christophe.pyree@gmail.com>
 * @package AppBundle\Form\Type
 */
class LoginType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                '_username',
                TextType::class
            )
            ->add(
                '_password',
                PasswordType::class
            )
            ->add(
                '_remember_me',
                CheckboxType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'valider',
                SubmitType::class
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
            ]
        );
    }


}

