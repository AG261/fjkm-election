<?php

namespace App\Form\Voting;

use App\Common\Constants\UserConstants;
use App\Entity\Voting\Candidat;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
                'required' => true,
            ])
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
                'required' => true,
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
            ->add('birthday', BirthdayType::class, [
                'label' => 'Naissance',
                'widget' => 'single_text',
                'html5' => false, // we disable HTML5 to use the datepicker
                'format' => 'dd/MM/yyyy',
                'attr' => [
                    'class' => 'flatpickr-input' // Ajoutez votre classe ici
                ],
                'row_attr' => [
                    'class' => 'fv-row mb-2'
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
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Candidat::class,
        ]);
    }
}
