<?php

namespace Gdev\IsaacBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class SeedsType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code', 'text')
            ->add('description', 'textarea')
    //         ->add('items', 'entity', array(
    // 'class' => 'GdevIsaacBundle:Item',
    // 'property' => 'name',
    // 'label'=> 'Coucou'
    // ))
            ->add('save',      'submit')
        ;

    }
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Gdev\IsaacBundle\Entity\Seeds'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gdev_isaacbundle_seeds';
    }
}
