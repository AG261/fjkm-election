<?php

namespace App\Form;

use App\Common\Constants\UserConstants;
use App\Entity\Account\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isProfil = isset($options['isProfil']) && $options['isProfil'] == true ? true : false ;
        $isClient = isset($options['isClient']) && $options['isClient'] == true ? true : false ;

        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'row_attr' => [
                    'class' => 'fv-row mb-2'
                ],
                'attr' => [
                    'placeholder' => 'Prénom',
                    'class' => 'form-control bg-transparent',
                    'autocomplete' => 'off'
                ],
                'required' => false,
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom',
                'row_attr' => [
                    'class' => 'fv-row mb-2'
                ],
                'attr' => [
                    'placeholder' => 'Nom',
                    'class' => 'form-control bg-transparent',
                    'autocomplete' => 'off'
                ],
                'required' => false,
            ])
            ->add('roles', ChoiceType::class, [
                'choices'  => array_flip(UserConstants::USER_ROLE_LIST),
                'label'    => 'Fonction',
                'multiple' => true, 
                'expanded' => true,
                'choice_attr' => function ($choice, string $key, mixed $value) use ($options) : array {

                    $class    = 'user_'.strtolower($value);
                    
                    return ['class' => $class];
                },
                
            ])
            ->add('email', TextType::class, [
                'label' => 'E-mail',
                'row_attr' => [
                    'class' => 'fv-row mb-2'
                ],
                'attr' => [
                    'placeholder' => 'E-mail',
                    'class' => 'form-control bg-transparent',
                    'autocomplete' => 'off'
                ],
                'required' => false,
                
            ])
            ->add('phone', TextType::class, [
                'label' => 'Téléphone',
                'row_attr' => [
                    'class' => 'fv-row mb-2'
                ],
                'attr' => [
                    'placeholder' => 'Téléphone',
                    'class' => 'form-control bg-transparent',
                    'autocomplete' => 'off'
                ],
                'required' => false,
                
            ])
            ->add('photo', FileType::class, [
                'label' => 'Photo',
                'mapped' => false,
                'row_attr' => [
                    'class' => 'fv-row mb-2'
                ],
                'attr' => [
                    'class' => 'form-control-file', 
                    'accept' => 'image/*', 
                ],
                'required' => false,
            ])            
            ->add('civility', ChoiceType::class, [
                'label' => 'Civilité',
                'choices' => array_flip(UserConstants::USER_CIVILITY_LIST),
                'row_attr' => [
                    'class' => 'fv-row mb-2'
                ],
                'attr' => [
                    'placeholder' => 'Civilité',
                    'class' => 'form-control bg-transparent',
                    'autocomplete' => 'off',
                    
                ],
                
                'required' => true,
            ])
            
            ->add('status', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => array_flip(UserConstants::USER_STATUS_LIST),
                
                'row_attr' => [
                    'class' => 'fv-row mb-2'
                ],
                'attr' => [
                    'placeholder' => 'Statut',
                    'class' => 'form-control bg-transparent',
                    'autocomplete' => 'off'
                ],
                'required' => true,
            ])
            
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => [
                    'class' => "form-control btn btn-primary",
                ],
                'row_attr'  => [
                    'class'     => 'col-md-4 mb-8'
                ],
            ])
            
        ;

        if(!empty($isProfil)){
            

            $builder->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Mot de passe non identique',
                'options' => [
                    'attr' => [
                        'class' => 'password form-control ',
                        'autocomplete' => 'off',
                    ],
                    'row_attr' => [
                        'class' => 'col-md-5 mb-0 field-pwd'
                    ],
                ],
                'mapped' => false,
                'required' => false,
                'first_options'  => [
                    'label' => 'Mot de passe',
                ],
                'second_options' => [
                    'label' => 'Mot de passe (Confirmation)',
                ],
            ]);

        }else{
            $builder->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'row_attr' => [
                    'class' => 'fv-row mb-3'
                ],
                'empty_data' => $isClient ? 'Client123456' : '',
                'attr' => [
                    'placeholder' => 'Mot de passe',
                    'class' => 'form-control bg-transparent',
                    'autocomplete' => 'off'
                ],
                'mapped' => false,
                'required' => false,
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'         => User::class,
            'validation_groups'  => ['user:write'],
            'cascade_validation' => true,
            'isClient'           => false,
            'isProfil'           => false
        ]);
    }
}
