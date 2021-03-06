<?php

namespace BCRM\WebBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EventRegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setMethod('POST')
            ->add('email', 'email', array('label' => 'E-Mail', 'required' => true, 'attr' => array('placeholder' => 'name@domain.de')))
            ->add('name', 'text', array('label' => 'Name', 'required' => true, 'attr' => array('placeholder' => 'Max Musterman')))
            ->add('tags', 'text', array('label' => 'Beschreibe deine Interesse in maximal 3 Tags (je 15 Zeichen)', 'required' => false, 'attr' => array('placeholder' => '#foo #bar #bcrm13', 'pattern' => '#[^\s]{1,15}( #[^\s]{1,15}){0,2}')))
            ->add('days', 'choice', array('label' => 'An welchen Tagen möchtest Du am BarCamp teilnehmen?', 'required' => true, 'choices' => array(3 => 'beide Tage', 1 => 'Samstag', 2 => 'Sonntag'), 'expanded' => true))
            ->add('arrival', 'choice', array('label' => 'Wie wirst Du anreisen?', 'required' => true, 'choices' => array('private' => 'Privat', 'public' => 'ÖPNV'), 'expanded' => true))
            ->add('save', 'submit', array('label' => 'Absenden'))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'      => 'BCRM\WebBundle\Form\EventRegisterModel',
            'csrf_protection' => false,
        ));
    }

    public function getName()
    {
        return 'event_register';
    }

}
