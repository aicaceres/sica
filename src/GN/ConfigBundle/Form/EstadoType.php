<?php

namespace GN\ConfigBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EstadoType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre',null,array('label'=>'Nombre:'))
            ->add('abreviatura',null,array('label'=>'Abreviatura:'))
            ->add('orden',null,array('label' => 'Orden: '))
            ->add('andnew','hidden',array('mapped' => false,'required' => false))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'GN\ConfigBundle\Entity\Estado'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gn_configbundle_estado';
    }
}
