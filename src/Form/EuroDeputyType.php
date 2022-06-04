<?php

namespace App\Form;

use App\Entity\EuroDeputy;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EuroDeputyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lastName', null, [
                'label' => 'Nom'
            ])
            ->add('firstName', null, [
                'label' => 'Prénom'
            ])
            ->add('gender', ChoiceType::class, [
                'label' => 'Sexe',
                'choices' => [
                    'H' => 'H',
                    'M' => 'M',
                    'F' => 'F'
                ],
                'placeholder' => '-- Choisir --'
            ])
            ->add('birthDate', DateType::class, [
                'attr' => [
                    'class' => 'date-picker'
                ],
                'widget' => 'single_text',
                'html5' => false,
                'label' => 'Date de naissance'
            ])
            ->add('professionCode', null, [
                'label' => 'Code'
            ])
            ->add('professionLabel', null, [
                'label' => 'Libellé'
            ])
            ->add('mandateStartDate', DateType::class, [
                'attr' => [
                    'class' => 'date-picker'
                ],
                'widget' => 'single_text',
                'html5' => false,
                'label' => 'Date début de mandate'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EuroDeputy::class,
        ]);
    }
}
