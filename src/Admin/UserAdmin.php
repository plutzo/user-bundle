<?php

declare(strict_types=1);


namespace Marlinc\UserBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\FieldDescription\FieldDescriptionInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Marlinc\UserBundle\Form\Type\RolesMatrixType;
use Marlinc\UserBundle\Entity\UserManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * @phpstan-extends AbstractAdmin<\Marlinc\UserBundle\Entity\UserInterface>
 */
class UserAdmin extends AbstractAdmin
{
    protected UserManagerInterface $userManager;

    public function __construct(UserManagerInterface $userManager)
    {
        parent::__construct();
        $this->userManager = $userManager;
    }

    protected function preUpdate(object $object): void
    {
        $this->userManager->updatePassword($object);
    }

    protected function configureFormOptions(array &$formOptions): void
    {
        $formOptions['validation_groups'] = ['Default'];

        if (!$this->hasSubject() || null === $this->getSubject()->getId()) {
            $formOptions['validation_groups'][] = 'Registration';
        } else {
            $formOptions['validation_groups'][] = 'Profile';
        }
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('email')
            ->add('enabled', null, ['editable' => true])
            ->add('createdAt')
            ->add('lastLogin');

        if ($this->isGranted('ROLE_ALLOWED_TO_SWITCH')) {
            $list
                ->add('impersonating', FieldDescriptionInterface::TYPE_STRING, [
                    'virtual_field' => true,
                    'template' => '@MarlincUser/Admin/Field/impersonating.html.twig',
                ]);
        }

        $list
            ->add(ListMapper::NAME_ACTIONS, null, [
                ListMapper::TYPE_ACTIONS => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ],
            ])
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('id')
            ->add('email');
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('email');
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->with('general', ['class' => 'col-md-4'])
                ->add('email')
                ->add('plainPassword', TextType::class, [
                    'required' => (!$this->hasSubject() || null === $this->getSubject()->getId()),
                ])
                ->add('enabled', null)
            ->end()
            ->with('roles', ['class' => 'col-md-8'])
                ->add('roles', RolesMatrixType::class, [
                    'label' => false,
                    'multiple' => true,
                    'required' => false,
                ])
            ->end();
    }

    protected function configureExportFields(): array
    {
        // Avoid sensitive properties to be exported.
        return array_filter(parent::configureExportFields(), static function (string $v): bool {
            return !\in_array($v, ['password', 'salt'], true);
        });
    }
}
