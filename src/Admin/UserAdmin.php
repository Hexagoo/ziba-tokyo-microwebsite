<?php
namespace App\Admin;

use App\Entity\User;
use App\Entity\Project;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Doctrine\ORM\EntityRepository;

final class UserAdmin extends AbstractAdmin
{
    public $supportsPreviewMode = true;

    protected function configureFormFields(FormMapper $formMapper)
    {
        function generateStrongPassword($length = 9, $add_dashes = false, $available_sets = 'luds')
        {
            $sets = array();
            if(strpos($available_sets, 'l') !== false)
              $sets[] = 'abcdefghjkmnpqrstuvwxyz';
            if(strpos($available_sets, 'u') !== false)
              $sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
            if(strpos($available_sets, 'd') !== false)
              $sets[] = '23456789';
            if(strpos($available_sets, 's') !== false)
              $sets[] = '!@#$%&*?';
            $all = '';
            $password = '';
            foreach($sets as $set)
            {
              $password .= $set[array_rand(str_split($set))];
              $all .= $set;
            }
            $all = str_split($all);
            for($i = 0; $i < $length - count($sets); $i++)
              $password .= $all[array_rand($all)];
            $password = str_shuffle($password);
            if(!$add_dashes)
              return $password;
            $dash_len = floor(sqrt($length));
            $dash_str = '';
            while(strlen($password) > $dash_len)
            {
              $dash_str .= substr($password, 0, $dash_len) . '-';
              $password = substr($password, $dash_len);
            }
            $dash_str .= $password;
            return $dash_str;
        }
        $randomPw = generateStrongPassword();

        $formMapper->add('projectName', TextType::class, [
                        'label'    => 'Name',
                        'required' => true,
                    ])
                    ->add('roles', CollectionType::class, [
                         'label'    => 'Role',
                         'entry_type'   => ChoiceType::class,
                         'entry_options'  => [
                             'choices'  => [
                                 'Admin' => 'ROLE_ADMIN',
                                 'User'     => 'ROLE_USER',
                             ],
                         ],
                     ]);

         // If we are in the edit page we don't display the password field
         $this->record_id = $this->request->get($this->getIdParameter());
          // if (!empty($this->record_id)) {
          //       $formMapper
          //       ->add('plainPassword', PasswordType::class, [
          //            'label'    => 'New password',
          //            'data' => true,
          //            'required' => false,
          //          ])
          //       ->add('password', PasswordType::class, [
          //            'label'    => 'New password confirmation',
          //            'data' => true,
          //            'required' => false,
          //          ]);
          // }

        if (empty($this->record_id)) {
          $formMapper->add('plainPassword', PasswordType::class, [
                     'label'    => 'Password',
                     'required' => true,
                   ])
                ->add('password', PasswordType::class, [
                     'label'    => 'Password confirmation',
                     'required' => true,
                   ])
                ->add('isActive', CheckboxType::class, [
                      'label' => 'User activated?',
                      'data' => true,
                      'required' => false,
                  ]);
        }
        $formMapper->add('projects', ModelAutocompleteType::class, array(
                        'by_reference' => false,
                        'property' => 'name',
                        'multiple' => true
                        ))
                    ->add('isActive', CheckboxType::class, [
                          'label' => 'User activated?',
                          'required' => false,
                      ]);
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('projectName');
        $listMapper->addIdentifier('roles');
        $listMapper->addIdentifier('projects');
        $listMapper->addIdentifier('isActive');
    }

    public function prePersist($object) {
        parent::prePersist($object);
        $this->updateUser($object);
    }

    public function preUpdate($object) {
        parent::preUpdate($object);
        $this->updateUser($object);
    }

    public function updateUser($object)
    {
        $plainPassword = $object->getPlainPassword();
        $password = $object->getPassword();

        if ($object instanceof User) {
          if ( strlen($plainPassword) > 0 ) {
            if ($plainPassword == $password) {
                $container = $this->getConfigurationPool()->getContainer();
                $encoder = $container->get('security.password_encoder');
                $encoded = $encoder->encodePassword($object, $plainPassword);

                $object->setPassword($encoded);
            } else {
                $this->getRequest()->getSession()->getFlashBag()->add("error_new_password", "Your passwords are different.");
                return false;
            }
          }
        }
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        // this text filter will be used to retrieve autocomplete fields
        $datagridMapper
            ->add('projectName')
        ;
    }

    public function toString($object)
    {
        return $object instanceof User
            ? $object->getUsername()
            : 'User'; // shown in the breadcrumb on the create view
    }
}
