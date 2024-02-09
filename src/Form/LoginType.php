<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', TextType::class, [
                'label' => false,
                'required' => true,
                'row_attr' => [
                    'class' => 'fv-row mb-8'
                ],
                'attr' => [
                    'placeholder' => 'Email',
                    'class' => 'form-control bg-transparent',
                    'autocomplete' => 'off'
                ]
            ])
            ->add('password', PasswordType::class, [
                'label' => false,
                'required' => true,
                'row_attr' => [
                    'class' => 'fv-row mb-3'
                ],
                'attr' => [
                    'placeholder' => 'Mot de passe',
                    'class' => 'form-control bg-transparent',
                    'autocomplete' => 'off'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'method' => Request::METHOD_POST,
            'csrf_protection' => true,
            'csrf_token_id' => 'authenticate',
            'attr' => [
                'novalidate' => 'novalidate',
                'class' => 'form w-100',
            ],
        ]);
    }
}
