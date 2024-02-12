<?php

namespace App\DataTable;

use App\Common\Constants\UserConstants;
use App\Entity\Account\User;
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

        /*<td>{{ candidat.id }}</td>
                <td>{{ candidat.firstname }}</td>
                <td>{{ candidat.lastname }}</td>
                <td>{{ candidat.civility }}</td>
                <td>{{ candidat.birthday ? candidat.birthday|date('Y-m-d H:i:s') : '' }}</td>
                <td>
                    <a href="{{ path('app_voting_candidat_show', {'id': candidat.id}) }}">show</a>
                    <a href="{{ path('app_voting_candidat_edit', {'id': candidat.id}) }}">edit</a>
                </td>*/

        $dataTable->add('id', TextColumn::class, [
                'field' => 'c.id',
                'label' => "#",
                'searchable' => false,
                'className' => "id",
                'visible' => false,
            ])
        ->add('lastname', TextColumn::class, [
            'field' => 'c.lastname',
            'label' => "Nom",
            'searchable' => true
        ])
        ->add('firstname', TextColumn::class, [
            'field' => 'c.firstname',
            'label' => "Prénom",
            'searchable' => true
        ]);
        $dataTable->add('civility', TextColumn::class, [
            'field' => 'c.civility',
            'label' => "Civilité",
            'searchable' => true,
        ]);

        $dataTable->add('buttons', TextColumn::class, [
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
                
                
            },
        ])
    ;
    }
}