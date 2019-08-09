<?php
namespace App\Admin;

use App\Entity\Project;
use App\Entity\HotTopic;
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

final class HotTopicAdmin extends AbstractAdmin
{
    public $supportsPreviewMode = true;

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('title', TextareaType::class)
                   ->add('icon', TextareaType::class);
    }

    // protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    // {
    //     $datagridMapper->add('name')
    //                    ->add('description');
    // }
    //
    // protected function configureListFields(ListMapper $listMapper)
    // {
    //     $listMapper->addIdentifier('name')
    //                ->add('backgroundImage')
    //                ->addIdentifier('description');
    // }

    public function toString($object)
    {
        return $object instanceof Project
            ? $object->getName()
            : 'Project'; // shown in the breadcrumb on the create view
    }
}
