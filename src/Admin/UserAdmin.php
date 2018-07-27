<?php

namespace Marlinc\UserBundle\Admin;

use Marlinc\AdminBundle\Admin\AbstractAdmin;
use Marlinc\EntityBundle\Admin\Filter\HasReferenceFilter;
use Marlinc\EntityBundle\Form\Type\EntityReferenceSelectType;
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
            ->add('groups')
            ->add('enabled', null, ['editable' => true])
            ->add('createdAt')
            ->add('allReferencingEntities', 'array')
            ->add('_action', null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ]
            ]);

        if ($this->isGranted('ROLE_ALLOWED_TO_SWITCH')) {
            $listMapper
                ->add('impersonating', 'string', ['template' => '@MarlincUser/Admin/Field/impersonating.html.twig']);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function configureTrashFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('email')
            ->add('enabled')
            ->add('deletedAt')
            ->add('_action', null, [
                'actions' => [
                    'untrash' => [],
                ]
            ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $filterMapper): void
    {
        $filterMapper
            ->add('email')
            ->add('person.firstname')
            ->add('person.lastname')
            ->add('person.newsletter')
            ->add('groups')
            ->add('referencingEntities', null, [], EntityReferenceSelectType::class, [
                'allow_edit' => true,
                'width' => '100%'
            ])
            ->add('has_reference', HasReferenceFilter::class)
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
                    ->add('email')
                    ->add('plainPassword', RepeatedType::class, [
                        'required' => (!$this->getSubject() || is_null($this->getSubject()->getId())),
                        'first_options' => ['label' => 'Password'],
                        'second_options' => ['label' => 'Repeat Password'],
                    ])
                    ->add('locale', LanguageType::class)
                ->end()
                ->with('Personal info', ['class' => 'col-md-6'])
                    ->add('person', AdminType::class, [
                        'delete' => false,
                        'by_reference' => true,
                    ])
                ->end()
            ->end()
            ->tab('Security')
                ->with('Status', ['class' => 'col-md-4'])
                    ->add('enabled', null, ['required' => false])
                ->end()
                ->with('Groups', ['class' => 'col-md-4'])
                    ->add('groups', 'sonata_type_model', [
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
     * Override form builder to set specific validation groups based on operation.
     *
     * {@inheritdoc}
     */
    public function getFormBuilder()
    {
        $this->formOptions['data_class'] = $this->getClass();

        $options = $this->formOptions;
        $options['validation_groups'] = (!$this->getSubject() || null === $this->getSubject()->getId()) ? 'Registration' : 'Profile';

        $formBuilder = $this->getFormContractor()->getFormBuilder($this->getUniqid(), $options);

        $this->defineFormBuilder($formBuilder);

        return $formBuilder;
    }

    /**
     * Avoid security fields to be exported.
     *
     * {@inheritdoc}
     */
    public function getExportFields()
    {
        return array_filter(parent::getExportFields(), function ($v) {
            return !in_array($v, ['plainPassword', 'password', 'salt']);
        });
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