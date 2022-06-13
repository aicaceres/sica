<?php

namespace GN\ConfigBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PersonalType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre',null,array('label'=>'Nombre y Apellido:'))
            ->add('activo',null,array('label' => 'Activo: ','required'=>false))
            ->add('andnew','hidden',array('mapped' => false,'required' => false))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'GN\ConfigBundle\Entity\Personal'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gn_configbundle_personal';
    }
}
