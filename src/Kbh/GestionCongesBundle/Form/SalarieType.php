<?php

namespace Kbh\GestionCongesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Kbh\GestionCongesBundle\Entity\Droits;


class SalarieType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('matricule')
            ->add('numeroCnps','text')
            ->add('civilite','choice',array(
                            'choices'=>array(
                                        'Mr'=>'Monsieur',
                                        'Mme'=>'Madame',
                                        'Mlle'=>'Mademoiselle')
                ))
            ->add('nom')
            ->add('prenom','text')
            ->add('dateNaissance', 'birthday',
                    array('widget' => 'single_text',
//                          'format' => 'dd-MM-yyyy',
//                          'years' => range(1950,1995)
                        ))
            ->add('statutMarital','choice',array(
                     'choices'=>array(
                    'célibataire'=>'célibataire',
                    'marié(e)'=>'marié(e)',
                    'divorcé(e)'=>'divorcé(e)',
                    'veuf/veuve'=>'veuf/veuve'
                )
            ))
            ->add('poste')
            ->add('email')
            ->add('image', new MediaType())
            ->add('telephone')
            ->add('statutEmploi','choice',array(
                            'choices'=>array(
                                'Actif'=>'Actif',
                                'Inactif'=>'Inactif'
                            )
            )) 
            ->add('dateEmbauche', 'date',
                   array('widget' => 'single_text',
//                          'format' => 'dd-MM-yyyy',
//                          'years' => range(1950,1995)
                        ))
            ->add('typeContratTravail','choice',array(
                            'choices'=>array(
                                'CDI'=>'Contrat à Durée Indéterminée',
                                'CDD'=>'Contrat à Durée Déterminée'
                            )
            ))
           ->add('superviseur','entity',array(
                    'class'=>'KbhGestionCongesBundle:Salarie',
                    'property'=>'poste'
                    ))
            ->add('unite','entity',array(
                    'class'=>'KbhGestionCongesBundle:OrganigrammeUnite',
                    'property'=>'nom'
                    ))
//            ->add('droits','collection', array('type' => new Droits()))    
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Kbh\GestionCongesBundle\Entity\Salarie'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'kbh_GestionCongesBundle_salarie';
    }
}
