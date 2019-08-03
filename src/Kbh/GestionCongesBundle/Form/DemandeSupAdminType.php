<?php

namespace Kbh\GestionCongesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DemandeSupAdminType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('salarie')
            ->add('typeDemande')
            ->add('dateDebut',             'date', array('widget' => 'single_text'))
            ->add('dateFin')
            ->add('dateDemande',                  'date', array('widget' => 'single_text')) 
            ->add('dateRefus',                  'date', array('widget' => 'single_text'))    
            ->add('dateRetour')    
            ->add('nbJoursOuvrables')
            ->add('motif')
            ->add('autreMotif')     
            ->add('localite')
            ->add('distance')
            ->add('estEnCours')     
            ->add('estRefuse')
            ->add('raisonRefus')
            ->add('estValideNiveau1')
            ->add('estValideNiveau2')
            ->add('estValideNiveau3')
            ->add('dateValidation1',                  'date', array('widget' => 'single_text')) 
            ->add('dateValidation2',                  'date', array('widget' => 'single_text'))    
            ->add('dateValidation3',            'date', array('widget' => 'single_text'))   
            ->add('nbNiveauxValidation')    
            ->add('estCloture')     
            ->add('dateCloture',                  'date', array('widget' => 'single_text')) 
            ->add('estValide')
            ->add('dateValidation',                  'date', array('widget' => 'single_text')) 
            ->add('soldeDroits')
            ->add('refusePar')
            ->add('valideurFinal')
            ->add('valideurEnCours')
            ->add('suppleantEnCours')
            ->add('valideurNiveau1')
            ->add('valideurNiveau2')
            ->add('valideurNiveau3')
            ->add('creeParSuperviseur')
            ->add('auteurDemande')
            ->add('pieceJointe')
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
