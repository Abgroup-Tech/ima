<?php

namespace Kbh\GestionCongesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AbsATSupAdminType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('salarie')
            ->add('admin')    
            ->add('motif')    
            ->add('dateDebut', 'date',
                   array('widget' => 'single_text',
                        ))     
            ->add('dateFin', 'date',
               array('widget' => 'single_text',
                    ))  
            ->add('dateCreation', 'date',
                   array('widget' => 'single_text',
                        ))      
            ->add('pieceJustificative') 
            ->add('medecin') 
            ->add('infoCabinetMedical')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Kbh\GestionCongesBundle\Entity\AbsencesAT'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'kbh_gestioncongesbundle_absencesat';
    }
}
