<?php

namespace Kbh\GestionCongesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class OrganigrammeUniteType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('sigle')
            ->add('manager')
            ->add('uniteSuivante1')
            ->add('uniteSuivante2')
            ->add('uniteSuivante3')  
            ->add('estService')
            ->add('estCellule')
            ->add('estDepartement')
            ->add('estDirection')
            ->add('estDga')
            ->add('estDg')    
            ->add('estDrh')
            ->add('valideurPourManager1')
            ->add('valideurPourManager2')
            ->add('valideurPourManager3')   
            ->add('nbNiveauxValidation')    
            ->add('salaries') 
            ->add('situationGeographique') 
            ->add('lieuTravail')     
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Kbh\GestionCongesBundle\Entity\OrganigrammeUnite'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'kbh_GestionCongesBundle_organigrammeunite';
    }
}
