<?php
namespace GN\ConfigBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ParametroType extends AbstractType
{
     /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $data = $options['data'];

        $builder->add('nombre',null,array('label' => 'Descripción:'));
        
        if( property_exists($data, 'abreviatura') )
            $builder->add('abreviatura',null,
                    array('label' => 'Abreviatura:','required'=>false));

        if( property_exists($data, 'persona') )
            $builder->add('persona','choice', array('label'=>'Persona:',
                'choices' => array( 'fisica' => 'Física','juridica' => 'Jurídica'),
                'data' => 'fisica', 'expanded'=>true, 'required'=>true));
        
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gn_configbundle_parametro';
    }
}