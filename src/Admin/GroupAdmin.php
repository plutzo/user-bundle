<?php
/**
 * Created by PhpStorm.
 * User: elias
 * Date: 28.03.17
 * Time: 16:49
 */

namespace Marlinc\UserBundle\Admin;

use Marlinc\EntityBundle\Form\Type\MachineNameType;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\UserBundle\Admin\Entity\GroupAdmin as BaseAdmin;

class GroupAdmin extends BaseAdmin
{
    protected function configureListFields(ListMapper $listMapper): void
    {
        parent::configureListFields($listMapper);
        $listMapper
            ->add('machineName')
            ->add('weight');
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        parent::configureFormFields($formMapper);

        $formMapper
            ->tab('Group')
                ->with('General')
                    ->add('machineName', MachineNameType::class, ['source_field' => 'name'])
                    ->add('weight')
                ->end()
            ->end();
    }

}