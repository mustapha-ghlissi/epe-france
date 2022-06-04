<?php

namespace App\Form;

use App\Entity\Accounting;
use App\Helpers\Year;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AccountingType extends AbstractType
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
            ->add('population', null, [
                'label' => 'Population légale en vigueur'
            ])
            ->add('groupingType', null, [
                'label' => 'Type groupement d\'appartenance'
            ])
            ->add('productsTotal', null, [
                'label' => 'Total des produits'
            ])
            ->add('localTax', null, [
                'label' => 'Impôts locaux'
            ])
            ->add('otherTax', null, [
                'label' => 'Autres impôts et taxes'
            ])
            ->add('globalAllocation', null, [
                'label' => 'Dotation globale'
            ])
            ->add('totalExpenses', null, [
                'label' => 'Total des charges'
            ])
            ->add('personalExpenses', null, [
                'label' => 'Charges de personnel'
            ])
            ->add('externalExpenses', null, [
                'label' => 'Achats et charges externes'
            ])
            ->add('financialExpenses', null, [
                'label' => 'Charges financières'
            ])
            ->add('grants', null, [
                'label' => 'Subventions versées'
            ])
            ->add('housingTax', null, [
                'label' => 'Taxe d\'habitation'
            ])
            ->add('propertyTax', null, [
                'label' => 'Taxe foncière sur les propriétés bâties'
            ])
            ->add('noPropertyTax', null, [
                'label' => 'Taxe foncière sur les propriétés non bâties'
            ])
            ->add('brankCredits', null, [
                'label' => 'Emprunts bancaires et dettes assimilées'
            ])
            ->add('receivedGrants', null, [
                'label' => 'Subventions reçues'
            ])
            ->add('equipmentExpenses', null, [
                'label' => 'Dépenses d\'équipement'
            ])
            ->add('creditRefund', null, [
                'label' => 'Remboursement d\'emprunts et dettes assimilées'
            ])
            ->add('debtAnnuity', null, [
                'label' => 'Annuité de la dette'
            ])
            ->add('source', null, [
                'label' => 'Source'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Accounting::class,
        ]);
    }
}
