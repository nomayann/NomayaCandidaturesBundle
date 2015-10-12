<?php

namespace Nomaya\Candidatures\CandidaturesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EntrepriseType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('tel')
            ->add('email')
            ->add('website')
            ->add('address1')
            ->add('address2')
            ->add('zip')
            ->add('city')
            ->add('effective')
            ->add('notes')
            ->add('pros')
            ->add('cons')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Nomaya\Candidatures\CandidaturesBundle\Entity\Entreprise'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'nomaya_candidatures_candidaturesbundle_entreprise';
    }
}