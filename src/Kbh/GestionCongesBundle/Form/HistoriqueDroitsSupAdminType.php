<?php

namespace Kbh\GestionCongesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class HistoriqueDroitsSupAdminType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('salarie')
            ->add('demande')    
            ->add('droits')
            ->add('conge')    
            ->add('absence')
            ->add('dateModification', 'date',
                   array('widget' => 'single_text',
                        ))
            ->add('soldeCongeAncien')
            ->add('soldeCongeNouveau')
            ->add('soldePermissionAncien')
            ->add('soldePermissionNouveau')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Kbh\GestionCongesBundle\Entity\HistoriqueDroits'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'kbh_gestioncongesbundle_historique_droits';
    }
}
