<?php

namespace Kbh\GestionCongesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BaseDroitsType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
//            ->add('estFemmedeMoinsDe21ans')
            ->add('nbEnfantsMineursAcharge','integer',array(
                'label'=>"Nombre d'enfants mineurs à charge"
            ))
//            ->add('aPlusDe5ansAnciennete')
//            ->add('aPlusDe10ansAnciennete')
//            ->add('aPlusDe15ansAnciennete')
//            ->add('aPlusDe20ansAnciennete')
//            ->add('aPlusDe25ansAnciennete')
            ->add('aMedailleHonneurTravail','choice',array(
                            'choices'=>array(
                                        1=>'Oui',
                                        0=>'Non'),
                            'label'=>'Titulaire de la médaille d\'honneur du Travail?',
                ))
            ->add('estLogeDansEntreprise','choice',array(
                            'choices'=>array(
                                        1=>'Oui',
                                        0=>'Non'),
                            'label'=>'Est logé(e) dans l\'établissement (ou à proximité) dont il a la garde et astreint à 
                            une durée de présence de 21 heures continues par jour ?'
                ))
        ->add('estExpatrieSejour1','choice',array(
                            'choices'=>array(
                                        1=>'Oui',
                                        0=>'Non')
                ))
        ->add('estExpatrieSejourSuivant','choice',array(
                            'choices'=>array(
                                        1=>'Oui',
                                        0=>'Non')
                ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Kbh\GestionCongesBundle\Entity\BaseDroits'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'kbh_gestioncongesbundle_basedroits';
    }
}
