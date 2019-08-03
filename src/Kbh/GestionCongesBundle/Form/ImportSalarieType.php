<?php

namespace Kbh\GestionCongesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class ImportSalarieType extends AbstractType
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
            ->add('dateNaissance')
            ->add('statutMarital','choice',array(
                     'choices'=>array(
                    'célibataire'=>'célibataire',
                    'marié'=>'marié(e)',
                    'divorcé'=>'divorcé(e)',
                    'veuf'=>'veuf/veuve'
                )
            ))
            ->add('poste')
            ->add('email')
            ->add('statutEmploi','choice',array(
                            'choices'=>array(
                                'actif'=>'actif',
                                'inactif'=>'inactif'
                            )
            ))   
             ->add('roleUtilisateur')        
//            ->add('image', new MediaType())
            ->add('telephone')
            ->add('dateEmbauche')
            ->add('typeContrat','choice',array(
                            'choices'=>array(
                                'CDI'=>'Contrat à Durée Indéterminée',
                                'CDD'=>'Contrat à Durée Déterminée'
                            )
            ))
           ->add('responsableDirect')
           ->add('posteResponsableDirect')      
            ->add('unite')
            ->add('droitsAcquis')
            ->add('droitsAnterieur')
            ->add('cumulDroits')   
            ->add('droitsPris')
            ->add('soldePermission')
            ->add('permissionsPrises')   
            ->add('soldeConges') ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Kbh\GestionCongesBundle\Entity\ImportSalarie'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'kbh_GestionCongesBundle_ImportSalarie';
    }
}
