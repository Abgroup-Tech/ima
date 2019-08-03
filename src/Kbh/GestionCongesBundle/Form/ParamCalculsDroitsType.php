<?php

namespace Kbh\GestionCongesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ParamCalculsDroitsType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('baseDroitsAcquis')
            ->add('joursSupAnciennete5ans')
            ->add('joursSupAnciennete10ans')
            ->add('joursSupAnciennete15ans')
            ->add('joursSupAnciennete20ans')
            ->add('joursSupAnciennete25ans')
            ->add('joursSupFemmeParEnfantMineur')
            ->add('joursSupMedailleHonneur')
            ->add('joursSupAstreinte')
            ->add('droitsExpatSejour1')
            ->add('droitsExpatBase')
            ->add('joursSupPr200hmois')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Kbh\GestionCongesBundle\Entity\ParamCalculsDroits'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'kbh_GestionCongesBundle_paramcalculsdroits';
    }
}
