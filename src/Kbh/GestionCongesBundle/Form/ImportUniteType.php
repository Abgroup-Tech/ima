<?php

namespace Kbh\GestionCongesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ImportUniteType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nomUnite')
            ->add('sigle')
            ->add('nomManager')
            ->add('matriculeManager')     
            ->add('posteManager')    
            ->add('uniteSuivante1')
            ->add('uniteSuivante2')
            ->add('uniteSuivante3')  
            ->add('typeUnite')
            ->add('managerUnite1')
            ->add('managerUnite2')
            ->add('managerUnite3')      
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Kbh\GestionCongesBundle\Entity\ImportUnite'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'kbh_GestionCongesBundle_ImportUnite';
    }
}
