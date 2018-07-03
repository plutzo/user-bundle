<?php
/**
 * Created by PhpStorm.
 * User: elias
 * Date: 28.03.17
 * Time: 16:49
 */

namespace Marlinc\UserBundle\Admin;

use Marlinc\AdminBundle\Admin\AbstractAdmin;
use Marlinc\EntityBundle\Form\Type\MachineNameType;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\UserBundle\Form\Type\SecurityRolesType;

class GroupAdmin extends AbstractAdmin
{
    /**
     * {@inheritdoc}
     */
    protected $formOptions = [
        'validation_groups' => 'Registration',
    ];

    /**
     * {@inheritdoc}
     */
    public function getNewInstance()
    {
        $class = $this->getClass();

        return new $class('', []);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper): void
    {
        parent::configureListFields($listMapper);
        $listMapper
            ->addIdentifier('name')
            ->add('roles')
            ->add('machineName')
            ->add('weight');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('name')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper): void
    {
        parent::configureFormFields($formMapper);
        $formMapper
            ->tab('Group')
                ->with('General', ['class' => 'col-md-6'])
                    ->add('name')
                    ->add('machineName', MachineNameType::class, ['source_field' => 'name'])
                    ->add('weight')
                ->end()
            ->end()
            ->tab('Security')
                ->with('Roles', ['class' => 'col-md-12'])
                    ->add('roles', SecurityRolesType::class, [
                        'expanded' => true,
                        'multiple' => true,
                        'required' => false,
                    ])
                ->end()
            ->end()
        ;
    }

}