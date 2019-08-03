<?php

namespace Kbh\GestionCongesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CongeEditType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateDebut', 'date',
                    array('widget' => 'choice',
                          'format' => 'dd-MM-yyyy',
                          'years' => range(2015,2015),
                          'label'=>'Date de début',
                        ))
            ->add('dateFin', 'date',
                    array('widget' => 'choice',
                          'format' => 'dd-MM-yyyy',
                          'years' => range(2015,2015),
                          'label'=>'Date de fin',
                        ))
            ->add('nbJoursOuvrables','integer',array(
                'label'=>'Durée en jours ouvrables'
            ))
            ->add('commentairesDemandeur','text',array(
                'label'=>'Commentaires'
            ))
            ->add('estValide','choice',array(
                'choices'=>array(
                    1=>'oui',
                    0=>'non'
                )
            ))
            ->add('estCloture','choice',array(
                'choices'=>array(
                    1=>'oui',
                    0=>'non'
                )
            ))
            
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
