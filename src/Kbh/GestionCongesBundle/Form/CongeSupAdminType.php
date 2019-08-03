<?php

namespace Kbh\GestionCongesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CongeSupAdminType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * Concerns the Administrator
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('salarie')
            ->add('demande')
            ->add('dateDebut', 'date',
                   array('widget' => 'single_text',
                        ))  
            ->add('dateFin') 
            ->add('dateRetour')      
            ->add('nbJoursOuvrables')
            
        ;
    }
    
 /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Kbh\GestionCongesBundle\Entity\Conge'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'kbh_gestioncongesbundle_conge';
    }
}
