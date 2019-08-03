<?php

namespace Kbh\GestionCongesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AbsencesAjustifierType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('justificatif', new PiecesJointesType())
            ->add('justifieAtemps','choice',array(
                            'choices'=>array(
                                        '1'=>'Oui',
                                        '0'=>'Non'),
                                        'expanded' => false,
                                          'multiple' => false,
                ))
          ->add('absenceJustifiable','choice',array(
                            'choices'=>array(
                                        '1'=>'Oui',
                                        '0'=>'Non'),
                                        'expanded' => false,
                                          'multiple' => false,
                ))      
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Kbh\GestionCongesBundle\Entity\AbsencesAjustifier'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'kbh_gestioncongesbundle_absencesajustifier';
    }
}
