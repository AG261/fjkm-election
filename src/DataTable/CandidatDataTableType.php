<?php

namespace App\DataTable;

use App\Entity\Voting\Candidat;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTable;
use Omines\DataTablesBundle\DataTableTypeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CandidatDataTableType extends AbstractController implements DataTableTypeInterface
{

    public function configure(DataTable $dataTable, array $options)
    {

        $dataTable
        
        ->add('id', TextColumn::class, [
                'field' => 'c.id',
                'label' => "#",
                'searchable' => false,
                'className' => "id",
                'visible' => false,
            ])
        ->add('firstname', TextColumn::class, [
            'field' => 'c.firstname',
            'label' => "Prénom",
            'searchable' => true,
            'visible' => false
        ])
        ->add('lastname', TextColumn::class, [
            'field' => 'c.lastname',
            'label' => "Nom",
            'searchable' => true,
            'visible' => false
        ])
        ->add('photo', TextColumn::class, [
            'field' => 'c.photo',
            'label' => 'Photo',
            'searchable' => false,
            'render' => function($value, $candidat) {
                
                $photo = $candidat->getPhoto();
                $image = "images/illustration/no-image.png" ;
                if(!empty($photo)){
                    $image = "upload/profil/".$photo ;
                }

                return $this->renderView('Admin/Element/datatable-image.html.twig', [
                    'url' => $image,
                    'name' => $candidat->getFirstname().' '.$candidat->getLastname(),
                ]);
            },
        ])
        ->add('numberid', TextColumn::class, [
            'field' => 'c.numberid',
            'label' => "Numéro",
            'searchable' => true
        ])
        ->add('number', TextColumn::class, [
            'field' => 'c.number',
            'label' => "Numéro à l'éléction",
            'searchable' => true,
            'visible' => false
        ])

        ->add('candidat', TextColumn::class, [
            
           // 'field' => 'u.firstName',
            'label' => "Nom",
            'searchable' => false,
            'render' => function($value, $candidat) {

                return trim($candidat->getCivility().' '.$candidat->getFirstname().' '.$candidat->getLastname(), '.');
            }
        ])


        ->add('buttons', TextColumn::class, [
            'label' => "Action",
            'orderable' => false,
            'searchable' => false,
            'className' => "button",
            'render' => function($value, Candidat $candidat)  use ($options){

                $route    = 'app.admin.voting.candidat.edit' ;

                $urls = [
                    ['name' => 'Edition', 'icon' => 'edit', 'path' => $route, 'params' => ['id' => $candidat->getId()]],
                ];
                return $this->renderView('Admin/Element/datatable-button.html.twig', [
                    'urls' => $urls
                ]);
            }
        ])
        ->addOrderBy('id', DataTable::SORT_ASCENDING)
        ->createAdapter(ORMAdapter::class, [
            'entity' => Candidat::class,
            'query' => function (QueryBuilder $builder) use ($options){
                $builder
                        ->from(Candidat::class, 'c')
                        ->select('c')
                ;
                
                if(isset($options['query']) && !empty($options['query'])){
                    $builder->andWhere('c.firstname LIKE :query OR c.lastname LIKE :query OR c.number LIKE :query OR c.numberid LIKE :query')
                            ->setParameter('query', '%'.$options['query'].'%');
                }

            },
        ])
    ;
    }
}