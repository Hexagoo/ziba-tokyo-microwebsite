<?php
namespace App\Admin;

use App\Entity\Activity;
use App\Entity\Phase;
use App\Entity\Project;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Sonata\MediaBundle\Form\Type\MediaType;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\Form\Type\CollectionType;
use Sonata\FormatterBundle\Form\Type\SimpleFormatterType;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

final class ActivityAdmin extends AbstractAdmin
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
                   ->add('phase', EntityType::class, [
                       'class' => Phase::class,
                       'choice_label' => function ($phase) {
                           return $phase->getName() . ' - ' . $phase->getProject();
                       }
                   ])
                   ->add('position', IntegerType::class)
                   ->add('activityHasMedias', CollectionType::class, array(
                            'constraints' => new \Symfony\Component\Validator\Constraints\Valid(),
                            'validation_groups' => true,
                        ), array(
                            'edit' => 'inline',
                            'inline' => 'table',
                            'sortable' => 'position',
                        )
                    );
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name')
                       ->add('phase')
                       ->add('position');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('name')
                   // ->addIdentifier('description')
                   ->addIdentifier('phase', EntityType::class, [
                       'class' => Phase::class,
                       'choice_label' => function ($phase) {
                           return $phase->getName() . ' - ' . $phase->getProject();
                       }
                   ])
                   ->addIdentifier('position');
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('move', $this->getRouterIdParameter().'/move/{position}');
    }

    public function prePersist($object)
    {
        // fix weird bug with setter object not being call
        $object->setActivityHasMedias($object->getActivityHasMedias());
        parent::prePersist($object);
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate($object)
    {
        $object->setActivityHasMedias($object->getActivityHasMedias());
        parent::preUpdate($object);

        // Remove all empty relation
        $mediaLinks = $object->getActivityHasMedias();
        foreach($mediaLinks as $mediaLink) {
          $currentMedia = $mediaLink->getMedia();
          if( is_null($currentMedia) ) {
            $container = $this->getConfigurationPool()->getContainer();
            $entityManager = $container->get('doctrine.orm.entity_manager');
            $entityManager->remove($mediaLink);
            $entityManager->flush();
          }
       }
    }

    public function postUpdate($object)
    {
        $this->removeMediaCollection();
    }

    function postPersist($object)
    {
        $this->removeMediaCollection();
    }

    function removeMediaCollection()
    {
        $container = $this->getConfigurationPool()->getContainer();
        $entityManager = $container->get('doctrine.orm.entity_manager');
        $q = $entityManager->createQuery('delete from App\Entity\ActivityHasMedia tb where tb.deleteMedia = 1');
        $q->execute();
    }

    public function toString($object)
    {
        return $object instanceof Activity
            ? $object->getName()
            : 'Activity'; // shown in the breadcrumb on the create view
    }
}
