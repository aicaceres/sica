<?php
namespace GN\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ControlType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fecha','date',array('widget' => 'single_text','label' => 'Fecha de Control:',
                'format' => "EEEE, d 'de' MMMM yyyy", 'required' => true))
            ->add('plantelCompleto','choice', array('label'=>'Plantel Completo:',
                'choices' => array( true => 'Si', false => 'No'),
                'expanded'=>true, 'required'=>true))
            ->add('sector')
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'GN\AdminBundle\Entity\Control'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gn_adminbundle_control';
    }
}
