<?php
namespace GN\ConfigBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LocalidadType extends AbstractType
{
     /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',null,array('label' => 'Nombre:', 'required' =>true))
            ->add('codpostal',null,array('label' => 'Código Postal:', 'required' =>false))    
            ->add('provincia','entity',array('label'=>'Provincia:',
                'class' => 'ConfigBundle:Provincia', 'required' =>true,
                'empty_value'   => 'Seleccione Provincia'))  
            ->add('pais','entity',array('label'=>'Pais:',
                'class' => 'ConfigBundle:Pais', 'mapped' =>false,
                'empty_value'   => 'Seleccione País'))  
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'GN\ConfigBundle\Entity\Localidad'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gn_configbundle_localidad';
    }
}