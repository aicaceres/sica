<?php

namespace GN\ConfigBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SubSectorType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre',null,array('label'=>'Nombre del Sub-Sector:'))
            ->add('activo',null,array('label' => 'Activo: ','required'=>false))
            ->add('sector','entity',array('label'=>'Sector:',
                'class' => 'ConfigBundle:Sector', 'required' =>true))

            ->add('tareas', 'collection', array(
                'type'           => new TareaType(),
                'by_reference'   => false,
                'allow_delete'   => true,
                'allow_add'      => true,
                'prototype_name' => 'itemform',
                'attr'           => array(
                    'class' => 'row item'
                )))
            ->add('andnew','hidden',array('mapped' => false,'required' => false))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'GN\ConfigBundle\Entity\SubSector'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gn_configbundle_subsector';
    }
}
