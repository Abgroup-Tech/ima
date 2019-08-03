<?php

namespace Kbh\GestionCongesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DemandeBySuperviseurType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateDebut',             'datetime', array('widget' => 'single_text'))
            ->add('dateFin',                 'hidden')
            ->add('dateRetour',           'hidden')    
            ->add('nbJoursOuvrables',  'integer')
            ->add('salarie'                   )       
            ->add('auteurDemande') 
            ->add('autreMotif')
            ->add('pieceJointe', new PiecesJointesType())
            ;   
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Kbh\GestionCongesBundle\Entity\Demande'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'kbh_gestioncongesbundle_demande';
    }
}
