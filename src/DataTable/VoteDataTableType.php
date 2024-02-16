<?php

namespace App\DataTable;

use App\Entity\Account\User;
use App\Entity\Voting\Candidat;
use App\Entity\Voting\Vote;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTable;
use Omines\DataTablesBundle\DataTableTypeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class VoteDataTableType extends AbstractController implements DataTableTypeInterface
{

    public function configure(DataTable $dataTable, array $options)
    {


        $dataTable
        
        ->add('id', TextColumn::class, [
                'field' => 'v.id',
                'label' => "#",
                'searchable' => false,
                'className' => "id",
                'visible' => false,
            ])
        
        ->add('num', TextColumn::class, [
            'field' => 'v.num',
            'label' => "Numéro",
            'searchable' => true
        ])
        ->add('firstname', TextColumn::class, [
            'field' => 'u.firstname',
            'label' => "Prénom",
            'searchable' => true,
            'visible' => false,
        ])
        ->add('lastname', TextColumn::class, [
            'field' => 'u.lastname',
            'label' => "Nom",
            'searchable' => true,
            'visible' => false,
        ])
        ->add('responsible', TextColumn::class, [
            'label' => "Responsable",
            'searchable' => false,
            'render' => function($value, Vote $vote) {
                $responsible = $vote->getUser() ;
                return $responsible->getFullName();
            }
        ])
        ->add('isDead', TextColumn::class, [
            'field' => 'v.isDead',
            'label' => "Vote null",
            'searchable' => false,
            'render' => function($value, Vote $vote) {
                if(!empty($vote->isIsDead())){
                    return '<i class="text-success" data-feather="check-square"></i>' ;
                }else{
                    return '<i class="text-danger" data-feather="x-square"></i>' ;
                }
                
            }
        ])
        ->add('isWhite', TextColumn::class, [
            'field' => 'v.isWhite',
            'label' => "Vote Blanc",
            'searchable' => false,
            'render' => function($value, Vote $vote) {
                if(!empty($vote->isIsWhite())){
                    return '<i class="text-success" data-feather="check-square"></i>' ;
                }else{
                    return '<i class="text-danger" data-feather="x-square"></i>' ;
                }
            }
        ])
        ;
        

        $dataTable->add('buttons', TextColumn::class, [
            'label' => "Action",
            'orderable' => false,
            'searchable' => false,
            'className' => "button",
            'render' => function($value, Vote $vote)  use ($options){

                $route    = 'app.admin.voting.vote.edit' ;

                $urls = [
                    ['name' => 'Edition', 'icon' => 'edit', 'path' => $route, 'params' => ['id' => $vote->getId()]],
                ];
                return $this->renderView('Admin/Element/datatable-button.html.twig', [
                    'urls' => $urls
                ]);
            }
        ])
        ->addOrderBy('id', DataTable::SORT_ASCENDING)
        ->createAdapter(ORMAdapter::class, [
            'entity' => Vote::class,
            'query' => function (QueryBuilder $builder) use ($options){
                $builder
                        ->from(Vote::class, 'v')
                        ->select('v')
                ;

                if(isset($options['query']) && !empty($options['query'])){
                    $builder->innerJoin(User::class,'u','WITH', 'v.user = u.id');
                    $builder->andWhere('v.num LIKE :query OR u.firstname LIKE :query OR u.lastname LIKE :query')
                            ->setParameter('query', '%'.$options['query'].'%');
                }

            },
        ])
    ;
    }
}