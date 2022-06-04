<?php

namespace App\Form;

use App\Entity\Tax;
use App\Helpers\Year;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaxType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('year', ChoiceType::class, [
                'choice_loader' => new CallbackChoiceLoader(function () {
                    return Year::getYears();
                }),
                'placeholder' => '-- Choisir --',
                'label' => 'Année'
            ])
            ->add('codeInsee', null, [
                'label' => 'Code commune'
            ])
            ->add('communeLabel', null, [
                'label' => 'Nom de la commune'
            ])
            ->add('communeLastName', null, [
                'label' => 'Nom de l\'élu'
            ])
            ->add('communeFirstName', null, [
                'label' => 'Prénom de l\'élu'
            ])
            ->add('communeBirthDate', DateType::class, [
                'attr' => [
                    'class' => 'date-picker'
                ],
                'widget' => 'single_text',
                'html5' => false,
                'label' => 'Date de naissance'
            ])
            ->add('departmentCode', null, [
                'label' => 'Code'
            ])
            ->add('departmentLabel', null, [
                'label' => 'Nom du département'
            ])
            ->add('depLastName', null, [
                'label' => 'Nom de l\'élu'
            ])
            ->add('depFirstName', null, [
                'label' => 'Prénom de l\'élu'
            ])
            ->add('depBirthDate', DateType::class, [
                'attr' => [
                    'class' => 'date-picker'
                ],
                'widget' => 'single_text',
                'html5' => false,
                'label' => 'Date de naissance'
            ])
            ->add('areaLabel', null, [
                'label' => 'Nom de la région'
            ])
            ->add('areaLastName', null, [
                'label' => 'Nom de l\'élu'
            ])
            ->add('areaFirstName', null, [
                'label' => 'Prénom de l\'élu'
            ])
            ->add('areaBirthDate', DateType::class, [
                'attr' => [
                    'class' => 'date-picker'
                ],
                'widget' => 'single_text',
                'html5' => false,
                'label' => 'Date de naissance'
            ])
            ->add('nbTaxHomes', null, [
                'label' => 'Nombre des foyers fiscaux'
            ])
            ->add('taxRevenue', null, [
                'label' => 'Revenu fixal de référence des foyers fiscaux'
            ])
            ->add('totalAmount', null, [
                'label' => 'Montant impôt net total'
            ])
            ->add('nbImposableTaxHomes', null, [
                'label' => 'Nombre des foyers fiscaux imposable'
            ])
            ->add('imposableTaxRevenue', null, [
                'label' => 'Revenu fixal de référence des foyers fiscaux imposée'
            ])
            ->add('salaryNbTaxHomes', null, [
                'label' => 'Nombre des foyers concernés par traitements et salaires'
            ])
            ->add('salaryTaxRevenue', null, [
                'label' => 'Montant de foyers concernés par traitement et salaires'
            ])
            ->add('pensionNbTaxHomes', null, [
                'label' => 'Nombre des foyers concernés retraites et pensions'
            ])
            ->add('pensionTaxRevenue', null, [
                'label' => 'Montant de foyers concernés retraites et pensions'
            ])
            ->add('source', null, [
                'label' => 'Source'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Tax::class,
        ]);
    }
}
