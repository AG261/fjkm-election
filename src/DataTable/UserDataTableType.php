<?php

namespace App\DataTable;

use App\Common\Constants\UserConstants;
use App\Entity\LocalArea;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTable;
use Omines\DataTablesBundle\DataTableTypeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserDataTableType extends AbstractController implements DataTableTypeInterface
{

    public function configure(DataTable $dataTable, array $options)
    {

        $dataTable->add('id', TextColumn::class, [
                'field' => 'u.id',
                'label' => "#",
                'searchable' => false,
                'className' => "id",
                'visible' => false,
            ])
        ->add('photo', TextColumn::class, [
            'field' => 'u.photo',
            'label' => 'Photo',
            'searchable' => false,
            'render' => function($value, $context) {
                
                $photo = $context->getPhoto();
                $image = "images/illustration/no-image.png" ;
                if(!empty($photo)){
                    $image = "upload/profil/".$photo ;
                }

                return $this->renderView('Element/datatable-image.html.twig', [
                    'url' => $image,
                    'name' => $context->getFullName(),
                ]);
            },
        ])
        ->add('firstName', TextColumn::class, [
            'field' => 'u.firstName',
            'label' => "Prénom",
            'searchable' => false,
            'render' => function($value, $user) {

                return trim($user->getFullName(), '-');
            }
        ]);
            $dataTable->add('lastName', TextColumn::class, [
                'field' => 'u.lastName',
                'label' => "Nom",
                'searchable' => false,
            ]);
        

        $dataTable->add('localArea', TextColumn::class, [
            'label' => "Zone",
            'searchable' => false,
            'render' => function($value, $user) {

                return !empty($user->getLocalArea()) ? $user->getLocalArea()->getName() : '-';
            }
        ]);

        

        $dataTable->add('buttons', TextColumn::class, [
            'label' => "Action",
            'orderable' => false,
            'searchable' => false,
            'className' => "button",
            'render' => function($value, $user)  use ($options){

                $route    = 'app.admin.user.team.edit' ;

                $urls = [
                    ['name' => 'Edition', 'icon' => 'edit', 'path' => $route, 'params' => ['id' => $user->getId()]],
                ];
                return $this->renderView('Element/datatable-button.html.twig', [
                    'urls' => $urls
                ]);
            }
        ])
        ->addOrderBy('id', DataTable::SORT_ASCENDING)
        ->createAdapter(ORMAdapter::class, [
            'entity' => User::class,
            'query' => function (QueryBuilder $builder) use ($options){
                $builder
                        ->from(User::class, 'u')
                        ->select('u')
                ;
                
                
            },
        ])
    ;
    }
}