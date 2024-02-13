<?php

namespace App\Form\Voting;

use App\Common\Constants\UserConstants;
use App\Constants\Content;
use App\Entity\Voting\Candidat;
use App\Entity\Voting\Vote;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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

class VoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            
            ->add('num', TextType::class, [
                'label' => 'Numéro',
                'row_attr' => [
                    'class' => 'fv-row mb-2'
                ],
                'attr' => [
                    'placeholder' => 'Numéro',
                    'class' => 'form-control bg-transparent',
                    'autocomplete' => 'off'
                ],
                'required' => false,
            ])
            
            ->add('isDead', ChoiceType::class, [
                'label' => 'Vote Mort',
                'choices' => array_flip(Content::VOTE_EXCEPTIONS),
                'row_attr' => [
                    'class' => 'fv-row mb-2'
                ],
                'attr' => [
                    'placeholder' => 'Vote Mort',
                    'class' => 'form-control bg-transparent',
                    'autocomplete' => 'off',

                ],
                'required' => false,
            ])
            ->add('isWhite', ChoiceType::class, [
                'label' => 'Vote Blanc',
                'choices' => array_flip(Content::VOTE_EXCEPTIONS),
                'row_attr' => [
                    'class' => 'fv-row mb-2'
                ],
                'attr' => [
                    'placeholder' => 'Vote Blanc',
                    'class' => 'form-control bg-transparent',
                    'autocomplete' => 'off',

                ],
                'required' => false,
            ])
            ->add('candidat', EntityType::class, [
                'label' => 'Candidat',
                'class' => Candidat::class,
                'query_builder' => function (EntityRepository $er) use ($options){

                    $query = $er->createQueryBuilder('c') ;
                    
                    $query->andWhere('c.status = :status')
                          ->setParameter('status', true)
                          ->orderBy('c.firstname', 'ASC')
                          ;
                          
                    return $query;
                },
                'choice_label' => function(Candidat $candidat){

                    return $candidat->getFullName() ;
                },
                'attr' => [
                    'data-control' => "select2",
                    'data-placeholder' => "Candidat",
                    'class' => "select2 form-select",
                ],
                'row_attr'  => [
                    'class'     => 'fv-row mb-2'
                ],
                'mapped' => false,
                'required' => false, 
                
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
            'data_class' => Vote::class,
        ]);
    }
}