<?php

namespace Marlinc\UserBundle\Admin;

use Marlinc\AdminBundle\Admin\AbstractAdmin;
use Marlinc\UserBundle\Form\Type\RolesMatrixType;
use Marlinc\UserBundle\Model\UserManagerInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\AdminType;
use Symfony\Component\Form\Extension\Core\Type\LanguageType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserAdmin extends AbstractAdmin
{
    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('email')
            ->add('realname')
            ->add('groups')
            ->add('enabled', null, ['editable' => true])
            ->add('allReferencingEntities', 'array')
        ;

        if ($this->isGranted('ROLE_ALLOWED_TO_SWITCH')) {
            $listMapper
                ->add('impersonating', 'string', [
                    'template' => '@MarlincUser/Admin/Field/impersonating.html.twig'
                ]);
        }

        parent::configureListFields($listMapper);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureTrashFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('email')
            ->add('enabled')
        ;

        parent::configureTrashFields($listMapper);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $filterMapper): void
    {
        $filterMapper
            ->add('realname')
            ->add('email')
            ->add('groups')
            ->add('referencingEntities', null, [],[
                'allow_edit' => true,
                'width' => '100%'
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->tab('User')
                ->with('User data', ['class' => 'col-md-6'])
                   ->add('realname')
                    ->add('email')
                    ->add('plainPassword', RepeatedType::class, [
                        'required' => (!$this->id($this->getSubject())),
                        'first_options' => ['label' => 'Password'],
                        'second_options' => ['label' => 'Repeat Password'],
                    ])
                    ->add('locale', LanguageType::class)
                ->end()
            ->end()
            ->tab('Security')
                ->with('Status', ['class' => 'col-md-4'])
                    ->add('enabled', null, ['required' => false])
                ->end()
                ->with('Groups', ['class' => 'col-md-4'])
                    ->add('groups', null , [
                        'required' => false,
                        'expanded' => true,
                        'multiple' => true,
                    ])
                ->end()
                ->with('Keys', ['class' => 'col-md-4'])
                    ->add('twoStepVerificationCode', null, ['required' => false])
                ->end()
                ->with('Roles', ['class' => 'col-md-12'])
                    ->add('roles', RolesMatrixType::class, [
                        'label' => 'form.label_roles',
                        'expanded' => true,
                        'multiple' => true,
                        'required' => false,
                    ])
                ->end()
            ->end();
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate($user): void
    {
        $this->getUserManager()->updateCanonicalFields($user);
        $this->getUserManager()->updatePassword($user);
    }

    /**
     * @param UserManagerInterface $userManager
     */
    public function setUserManager(UserManagerInterface $userManager): void
    {
        $this->userManager = $userManager;
    }

    /**
     * @return UserManagerInterface
     */
    public function getUserManager()
    {
        return $this->userManager;
    }
}