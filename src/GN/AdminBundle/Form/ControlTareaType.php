<?php
namespace GN\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ControlTareaType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id','hidden')    
            ->add('fecha','date',array('widget' => 'single_text','label' => 'Fecha:',
                'format' => 'dd-MM-yyyy',  'required' => true))
            ->add('hora','time',array('widget' => 'single_text','label' => 'Hora:', 'required' => true))   
            ->add('nombrePersonal')   
            ->add('personal','entity',array('label'=>'Personal:',
                'class' => 'ConfigBundle:Personal', 'required' =>false))  
            ->add('estado','entity',array('label'=>'Estado:',
                'class' => 'ConfigBundle:Estado', 'required' =>true))  
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'GN\AdminBundle\Entity\ControlTarea'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'sg_adminbundle_controltarea';
    }
}
