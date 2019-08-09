<?php
namespace App\Admin;

use App\Entity\Phase;
use App\Entity\Project;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

final class PhaseAdmin extends AbstractAdmin
{
    public $supportsPreviewMode = true;

    protected $datagridValues = [
        '_page' => 1,
        '_sort_order' => 'ASC',
        '_sort_by' => 'position',
    ];

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('name', TextType::class)
                   ->add('description', TextareaType::class)
                   ->add('image', ModelType::class, array(), array('link_parameters' => array(
                            'context' => 'default',
                            'provider' => 'sonata.media.provider.image',
                    )))
                   ->add('position', IntegerType::class)
                   ->add('project', EntityType::class, [
                       'class' => Project::class,
                       'choice_label' => 'name'
                    ]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name');
        $datagridMapper->add('description');
        $datagridMapper->add('project');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('name');
        $listMapper->addIdentifier('image');
        $listMapper->addIdentifier('project');
        $listMapper->addIdentifier('_action', 'actions', array(
             'actions' => array(
                 'floorplan' => array('template' => '/override/phase_button.html.twig'),
             )
         ));
        $listMapper->addIdentifier('position');
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('move', $this->getRouterIdParameter().'/move/{position}');
    }

    public function toString($object)
    {
        return $object instanceof Phase
            ? $object->getName()
            : 'Phase'; // shown in the breadcrumb on the create view
    }
}
