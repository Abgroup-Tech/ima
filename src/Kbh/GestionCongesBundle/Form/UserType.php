<?php

namespace Kbh\GestionCongesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', null, array('label' => 'form.username', 'translation_domain' => 'FOSUserBundle'))
            ->add('roles','choice',array(
                            'choices'=>array(
                                        'ROLE_SALARIE'=>'Salarie',
                                        'ROLE_SUPERVISEUR'=>'Superviseur',
                                        'ROLE_TOP_MANAGER'=>'Top Manager',
                                        'ROLE_ADMIN'=>'Admin'),
                            'multiple' => true,
                            'expanded' => false,
                )) 
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Kbh\UserBundle\Entity\User'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'kbh_userbundle_user';
    }
}
