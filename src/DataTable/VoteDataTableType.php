<?php

namespace App\DataTable;

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
        ->add('responsible', TextColumn::class, [
            'field' => 'v.responsible',
            'label' => "Responsable",
            'searchable' => true,
            'render' => function($value, Vote $vote) {
                $responsible = $vote->getUser() ;
                return $responsible->getFullName();
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
            },
        ])
    ;
    }
}