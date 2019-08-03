<?php

namespace Kbh\GestionCongesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class Absence2Type extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * Concerns the Supervisor
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
           ->add('salarie','entity', array(
                                        'class' => 'KbhGestionCongesBundle:Salarie',
                                        'property' => 'nomprenom',
                                    ))
            ->add('type','entity', array(
                                        'class' => 'KbhGestionCongesBundle:ParamPermissions',
                                        'property' => 'motif',
                                        'label' => 'Motif'
                                    ))
            ->add('dateDebut')
            ->add('dateFin')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Kbh\GestionCongesBundle\Entity\Absence'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'kbh_GestionCongesBundle_absence';
    }
}
