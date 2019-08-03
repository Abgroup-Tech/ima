<?php

namespace Kbh\GestionCongesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ReportSupAdminType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nbJoursOuvrables')
            ->add('dateDebut', 'date', array('widget' => 'single_text'))
            ->add('dateFin')
            ->add('dateRetour')
            ->add('dateReport', 'date', array('widget' => 'single_text'))
            ->add('salarie',  'entity',array('class'=>'KbhGestionCongesBundle:Salarie',) ) 
            ->add('conge',  'entity',array('class'=>'KbhGestionCongesBundle:Conge',) )   
            ->add('manager',  'entity',array('class'=>'KbhGestionCongesBundle:Salarie',) )    
            ->add('typeReport',  'text') 
            ->add('ancienneDateDebut')
            ->add('ancienneDateFin')
            ->add('motifReport', 'text') 
             
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Kbh\GestionCongesBundle\Entity\Report'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'kbh_gestioncongesbundle_report';
    }
}
