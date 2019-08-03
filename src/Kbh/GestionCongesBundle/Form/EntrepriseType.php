<?php

namespace Kbh\GestionCongesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EntrepriseType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('nbSalaries')
            ->add('delaisMiseAjours','choice',array(
                            'choices'=>array(
                                       '30'=>'Chaque mois')
                ))
           ->add('delaisMiseAjoursAnnuel','choice',array(
                            'choices'=>array(
                                       '365'=>'Chaque année(s)'
//                                       '180'=>'Chaque semestre(s)',
//                                       '90'=>'Chaque trimestre(s)',
//                                       '30'=>'Chaque mois',
                                )
                ))     
//            ->add('delaisClearCache','choice',array(
//                              'choices'=>array(
//                                        '7'=>'Chaque semaine',
//                                        '14'=>'Chaque deux semaine(s)',
//                                        '30'=>'Chaque mois')
//                ))
            ->add('dateImplementation', 'date',
                   array('widget' => 'single_text',
                        ))   
           ->add('dateLivraison', 'date',
                   array('widget' => 'single_text',
                        ))
            ->add('dateUpdateMensuel')
            ->add('dateUpdateAnnuel')    
            ->add('logo', new MediaType())
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Kbh\GestionCongesBundle\Entity\Entreprise'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'kbh_GestionCongesBundle_entreprise';
    }
}
