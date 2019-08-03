<?php

namespace Kbh\GestionCongesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ReportType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nbJoursOuvrables',  'hidden', array('required' => false,))
            ->add('dateDebut', 'date', array('widget' => 'single_text'))
			->add('dateFin', 'text', array('required' => false,))
			->add('dateRetour', 'text', array('required' => false,))
/* 			->add('salarie',  'entity',array('class'=>'KbhGestionCongesBundle:Salarie',) ) 
		    ->add('conge',  'entity',array('class'=>'KbhGestionCongesBundle:Conge',) )   
		    ->add('manager',  'entity',array('class'=>'KbhGestionCongesBundle:Salarie',) )    
		    ->add('typeReport',  'text') 
		    ->add('ancienneDateDebut',  'datetime', array('widget' => 'single_text'))
		    ->add('ancienneDateFin', 'datetime', array('widget' => 'single_text'))*/
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
