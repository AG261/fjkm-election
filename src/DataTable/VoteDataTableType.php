<?php

namespace App\DataTable;

use App\Constants\Content;

use App\Entity\Account\User;
use App\Entity\Voting\Candidat;
use App\Entity\Voting\Vote;
use App\Services\Common\Utils;
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
        ->add('status', TextColumn::class, [
            'field' => 'v.status',
            'label' => "Etat",
            'searchable' => true,
            'render' => function($value, Vote $vote) {
                $status = $vote->getStatus();
                $voteStatus = Content::VOTE_STATUS_LIST ;
                
                $value  = $voteStatus[Content::VOTE_STATUS_NOT_VERIFY] ;
                $class  = 'text-warning';
                if($status == Content::VOTE_STATUS_VERIFY_NOT_VALID){
                    $value  = $voteStatus[Content::VOTE_STATUS_VERIFY_NOT_VALID] ;
                    $class  = 'text-danger';
                }
                if($status == Content::VOTE_STATUS_VERIFY_VALID){
                    $value  = $voteStatus[Content::VOTE_STATUS_VERIFY_VALID] ;
                    $class  = 'text-success';
                }

                return '<span class="'.$class.'">'.$value.'</span>' ;
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

                    $query  = $options['query'] ;
                    
                    $sql    = 'v.num LIKE :query OR u.firstname LIKE :query OR u.lastname LIKE :query';
                    $builder->innerJoin(User::class,'u','WITH', 'v.user = u.id');

                    //Search in array
                    $utils  = new Utils();
                    $searchsStatus = $utils->arraySearchLike(Content::VOTE_STATUS_LIST, '%'.str_replace("+"," ",$query).'%') ;
                    $status = '';
                    if(count($searchsStatus) > 0){
                        $status = array_key_first($searchsStatus);
                        
                    }

                    if(!empty($status)){
                        $sql    = $sql.' OR v.status = :status';
                    }

                    $builder->andWhere($sql)
                            ->setParameter('query', '%'.$query.'%');
                            
                    if(!empty($status)){
                        $builder->setParameter('status', $status);
                    }
                    
                }

            },
        ])
    ;
    }
}