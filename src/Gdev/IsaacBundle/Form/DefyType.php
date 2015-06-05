<?php

namespace Gdev\IsaacBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DefyType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('seed')
            ->add('description')
            ->add('record')
            ->add('file', 'file', array('required'=>true))
            //->add('idUser')
            //->add('date')
            ->add('save',      'submit')
        ;
    }
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Gdev\IsaacBundle\Entity\Defy'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gdev_isaacbundle_defy';
    }
}
