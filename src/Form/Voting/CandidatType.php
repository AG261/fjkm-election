<?php

namespace App\Form\Voting;

use App\Constants\UserConstants;
use App\Entity\Voting\Candidat;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\NotBlank;

class CandidatType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('number', TextType::class, [
                'label' => "Numéro à l'éléction",
                'row_attr' => [
                    'class' => 'fv-row mb-2'
                ],
                'attr' => [
                    'placeholder' => "Numéro à l'éléction",
                    'class' => 'form-control bg-transparent',
                    'autocomplete' => 'off'
                ],
                'required' => false,
            ])
            ->add('numberid', TextType::class, [
                'label' => "Numéro à l'eglise",
                'row_attr' => [
                    'class' => 'fv-row mb-2'
                ],
                'attr' => [
                    'placeholder' => "Numéro à l'eglise",
                    'class' => 'form-control bg-transparent',
                    'autocomplete' => 'off'
                ],
                'required' => false,
            ])
            /*
            ->add('firstname', TextType::class, [
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
            */
            ->add('lastname', TextType::class, [
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
            ->add('civility', ChoiceType::class, [
                'label' => 'Civilité',
                'choices' => array_flip(Candidat::CANDIDAT_CIVILITY_LIST),
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
                'required' => false,
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => [
                    'class' => "form-control btn btn-primary btn-save",
                ],
                'row_attr'  => [
                    'class'     => 'col-md-4 mb-8'
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Candidat::class,
            'validation_groups'  => ['candidat:write'],
            'cascade_validation' => true,
        ]);
    }
}
