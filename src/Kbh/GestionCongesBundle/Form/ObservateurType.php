<?php

namespace Kbh\GestionCongesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ObservateurType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('salarie','entity', array(
                                        'class' => 'KbhGestionCongesBundle:Salarie',
                                        'property' => 'poste',
                                        'label' => 'Choisissez le salarié qui deviendra Valideur :'
                                    ))    
            ->add('suppleant','entity', array(
                                        'class' => 'KbhGestionCongesBundle:Salarie',
                                        'property' => 'nomprenom',
                                        'label' => 'Choisir le salarié qui le suppléra en cas de besoin :'
                                    ))    
            
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Kbh\GestionCongesBundle\Entity\Observateur'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'kbh_gestioncongesbundle_observateur';
    }
}
