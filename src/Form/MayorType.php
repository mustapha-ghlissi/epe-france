<?php

namespace App\Form;

use App\Entity\Mayor;
use App\Helpers\Year;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MayorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('latitude', null, [
                'label' => 'Latitude'
            ])
            ->add('longitude', null, [
                'label' => 'Longitude'
            ])
            ->add('departmentCode', null, [
                'label' => 'Code'
            ])
            ->add('departmentLabel', null, [
                'label' => 'Libellé'
            ])
            ->add('departmentCapital', null, [
                'label' => 'Chef-Lieu'
            ])
            ->add('departmentPopulation', null, [
                'label' => 'Population'
            ])
            ->add('departmentSurface', null, [
                'label' => 'Superficie (km²)'
            ])
            ->add('departmentDensity', null, [
                'label' => 'Densité (Hab./km²)'
            ])
            ->add('areaLabel', null, [
                'label' => 'Libellé'
            ])
            ->add('nbDepartments', null, [
                'label' => 'Nombre des départements'
            ])
            ->add('areaCapital', null, [
                'label' => 'Chef-Lieu'
            ])
            ->add('areaSurface', null, [
                'label' => 'Superficie (km²)'
            ])
            ->add('areaPopulation', null, [
                'label' => 'Population'
            ])
            ->add('areaDensity', null, [
                'label' => 'Densité (Hab./km²)'
            ])
            ->add('codeInsee', null, [
                'label' => 'Code INSEE'
            ])
            ->add('communeLabel', null, [
                'label' => 'Libellé'
            ])
            ->add('zipCode', null, [
                'label' => 'Code postal'
            ])
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
            ->add('functionStartDate', DateType::class, [
                'attr' => [
                    'class' => 'date-picker'
                ],
                'widget' => 'single_text',
                'html5' => false,
                'label' => 'Date début de fonction'
            ])
            ->add('nbPublicService', null, [
                'label' => 'Nombre de service public'
            ])
            ->add('populationYear', ChoiceType::class, [
                'label' => 'Année de la population',
                'choice_loader' => new CallbackChoiceLoader(function() {
                    return Year::getYears();
                }),
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Mayor::class,
        ]);
    }
}
