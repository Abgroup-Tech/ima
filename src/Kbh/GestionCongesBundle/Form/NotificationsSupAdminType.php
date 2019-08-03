<?php

namespace Kbh\GestionCongesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class NotificationsSupAdminType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('demande')
           ->add('salarie')    
            ->add('observateur')
            ->add('estTraitee')
            ->add('vuParDemandeur')
            ->add('vuParValideurEnCours')
            ->add('vuParValideurPrecedent')
            ->add('vuParObservateur')
            ->add('vuParSupN1')
            ->add('messageDemandeur')
            ->add('messageValideurEnCours')
            ->add('messageValideurPrecedent')
            ->add('messageValideurSuivant')
            ->add('dateEnvoi', 'date', array('widget' => 'single_text'))
            ->add('messageFinal')
            ->add('valideurEnCours')
            ->add('valideurPrecedent')
            ->add('valideurSuivant')
            ->add('superieurN1') 
            ->add('admin')
            ->add('vuParAdmin')
             
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Kbh\GestionCongesBundle\Entity\Notification'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'kbh_gestioncongesbundle_notification';
    }
}
