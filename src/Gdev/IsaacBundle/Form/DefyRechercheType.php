<?php

namespace Gdev\IsaacBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class DefyRechercheType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('motcle', 'text', array('label' => 'Mot-clé'));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'acteurrecherche';
    }
}