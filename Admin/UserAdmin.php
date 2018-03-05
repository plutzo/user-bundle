<?php

namespace Marlinc\UserBundle\Admin;

use Marlinc\UserBundle\Event\UserAclUpdateEvent;
use Misd\PhoneNumberBundle\Form\Type\PhoneNumberType;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Security\Acl\Permission\MaskBuilder;
use Sonata\AdminBundle\Security\Handler\AclSecurityHandlerInterface;
use Sonata\UserBundle\Admin\Model\UserAdmin as BaseAdmin;
use Sonata\UserBundle\Form\Type\SecurityRolesType;
use Sonata\UserBundle\Form\Type\UserGenderListType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\LanguageType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Security\Acl\Domain\Acl;
use Symfony\Component\Security\Acl\Domain\Entry;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Model\MutableAclInterface;

class UserAdmin extends BaseAdmin
{
    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('email')
            ->add('groups')
            ->add('enabled', null, array('editable' => true))
            ->add('createdAt')
            ->add('_action', null, array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                    'delete' => array(),
                )
            ))
        ;

        if ($this->isGranted('ROLE_ALLOWED_TO_SWITCH')) {
            $listMapper
                ->add('impersonating', 'string', array('template' => 'SonataUserBundle:Admin:Field/impersonating.html.twig'))
            ;
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $filterMapper): void
    {
        $filterMapper
            ->add('email')
            ->add('person.newsletter')
            ->add('groups');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper): void
    {
        // define group zoning
        $formMapper
            ->tab('User')
                ->with('User data', array('class' => 'col-md-6'))->end()
                ->with('Personal info', array('class' => 'col-md-6'))->end()
            ->end()
            ->tab('Security')
                ->with('Status', array('class' => 'col-md-4'))->end()
                ->with('Groups', array('class' => 'col-md-4'))->end()
                ->with('Keys', array('class' => 'col-md-4'))->end()
                ->with('Roles', array('class' => 'col-md-12'))->end()
            ->end()
        ;

        $formMapper
            ->tab('User')
                ->with('User data')
                    ->add('email')
                    ->add('plainPassword', RepeatedType::class, array(
                        'required' => (!$this->getSubject() || is_null($this->getSubject()->getId())),
                        'first_options'  => array('label' => 'Password'),
                        'second_options' => array('label' => 'Repeat Password'),
                    ))
                ->end()
                ->with('Personal info')
                    // TODO: Embed PersonAdmin
                    ->add('locale', LanguageType::class)
                    ->add('person.gender', UserGenderListType::class)
                    ->add('person.firstname')
                    ->add('person.lastname')
                    ->add('person.phone', PhoneNumberType::class, [
                        'default_region' => 'DE',
                        'widget' => PhoneNumberType::WIDGET_COUNTRY_CHOICE,
                        'country_choices' => ['DE', 'AT', 'CH'],
                        'required' => false
                    ])
                    ->add('dateOfBirth', DateType::class, [
                        'years' => range(date('Y') - 90, date('Y') - 15),
                        'required' => false
                    ])
                ->end()
            ->end()
            ->tab('Security')
                ->with('Status')
                    ->add('enabled', null, array('required' => false))
                ->end()
                ->with('Groups')
                    ->add('groups', 'sonata_type_model', array(
                        'required' => false,
                        'expanded' => true,
                        'multiple' => true,
                    ))
                ->end()
                ->with('Roles')
                    ->add('roles', SecurityRolesType::class, array(
                        'label' => 'form.label_roles',
                        'expanded' => true,
                        'multiple' => true,
                        'required' => false,
                    ))
                ->end()
                ->with('Keys')
                    ->add('twoStepVerificationCode', null, array('required' => false))
                ->end()
            ->end()
        ;
    }

    public function preUpdate($user): void
    {
        parent::preUpdate($user);

        $originalData = $this->getConfigurationPool()
            ->getContainer()->get('doctrine')->getManager()
            ->getUnitOfWork()->getOriginalEntityData($user);

        if ($originalData['email'] !== $user->getEmail()) {
            $this->getConfigurationPool()->getContainer()->get('security.acl.provider')->updateUserSecurityIdentity(
                UserSecurityIdentity::fromAccount($user), $originalData['email']);
        }
    }

    /**
     * @inheritdoc
     */
    public function postUpdate($object)
    {
        $this->updateAces($object);
    }

    /**
     * @inheritdoc
     */
    public function postPersist($object)
    {
        $this->updateAces($object);
    }

    private function updateAces($user) {
        $securityIdentity = UserSecurityIdentity::fromAccount($user);
        $securityHandler = $this->getSecurityHandler();

        // Create/update ACEs for entities.
        if ($securityHandler instanceof AclSecurityHandlerInterface) {
            // Get object identities and corresponding ace bitmasks.
            $event = new UserAclUpdateEvent($user);
            $dispatcher = $this->getConfigurationPool()->getContainer()->get('event_dispatcher');
            $dispatcher->dispatch(UserAclUpdateEvent::NAME, $event);
            $masks = $event->getMasks();

            foreach ($event->getObjectIdentities() as $key => $objectIdentity) {
                $acl = $securityHandler->getObjectAcl($objectIdentity);
                $maskBuilder = new MaskBuilder(($masks[$key] === false)?0:$masks[$key]);

                // Flag to determine if mask was really updated or not.
                $isUpdated = false;

                // Does ACL exist? If not, create one.
                if ($acl instanceof MutableAclInterface) {
                    // Go through all ACEs and try to find current user's ACE.
                    foreach ($acl->getObjectAces() as $akey => $ace) {
                        if ($ace instanceof Entry && $ace->getSecurityIdentity()->equals($securityIdentity)) {
                            if ($masks[$key] === false) {
                                // Mask is false -> remove existing ACE.
                                $acl->deleteObjectAce($akey);
                            } else {
                                // Set the mask for the existing ACE.
                                $acl->updateObjectAce($akey, $maskBuilder->get());
                            }

                            $isUpdated = true;
                            break;
                        }
                    }
                } else {
                    $acl = $securityHandler->createAcl($objectIdentity);
                }

                // Need to insert new ACE.
                if (!$isUpdated && $acl instanceof Acl && $masks[$key] !== false) {
                    $acl->insertObjectAce($securityIdentity, $maskBuilder->get());
                }
                $securityHandler->updateAcl($acl);
            }
        }
    }
}