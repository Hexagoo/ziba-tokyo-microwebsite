<?php
namespace App\Admin;

use App\Entity\Project;
use App\Entity\HotTopic;
use App\Entity\User;
use App\Admin\HotTopicAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Sonata\MediaBundle\Form\Type\MediaType;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\Form\Type\CollectionType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\CoreBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\Type\ModelType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;

final class ProjectAdmin extends AbstractAdmin
{
    public $supportsPreviewMode = true;

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
                   ->with('Content')
                     ->add('name', TextType::class)
                     ->add('backgroundImage', ModelType::class, array('label' => 'Image'), array(), array(
                              'query_builder' => function (\Doctrine\ORM\EntityRepository $repository) {
                                      return $repository->createQueryBuilder('m')
                                              ->where('m.content_type = image/jpeg')
                                              ->add('orderBy', 'm.name ASC');
                                  }
                          ))
                     ->add('description', TextareaType::class)
                     ->add('users', ModelAutocompleteType::class, array(
                             'by_reference' => false,
                             'property' => 'projectName',
                             'multiple' => true
                           ))
                     ->add('isActivated', CheckboxType::class, [
                           'label' => 'Project activated?',
                           'required' => false,
                       ])
                   ->end()
                   ->with('Meta-data')
                     ->add('hotTopic', CollectionType::class, array(
                                'by_reference' => false // !important
                              ), array(
                                  'allow_delete' => true,
                                  'edit' => 'inline',
                                  'inline' => 'table'
                              ))
                   ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name')
               //         ->add('backgroundImage', 'doctrine_orm_callback', [
               // //                'callback'   => [$this, 'getWithOpenCommentFilter'],
               //                 'callback' => function($queryBuilder, $alias, $field, $value) {
               //                     if (!$value['value']) {
               //                         return;
               //                     }
               //
               //                     $queryBuilder
               //                     ->where('m.content_type = image/jpeg')
               //                     ->add('orderBy', 'm.name ASC');
               //                     return true;
               //                 },
               //                 'field_type' => ModelType::class
               //             ])
                       ->add('description');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $slug_project = "ziba-project";
        $listMapper->addIdentifier('name')
                   ->addIdentifier('backgroundImage')
                   ->addIdentifier('description')
                   ->addIdentifier('isActivated')
                   ->addIdentifier('_action', 'actions', array(
                        'actions' => array(
                            'floorplan' => array('template' => '/override/project_button.html.twig'),
                        )
                    ));
    }

    public function toString($object)
    {
        return $object instanceof Project
            ? $object->getName()
            : 'Project'; // shown in the breadcrumb on the create view
    }
}
