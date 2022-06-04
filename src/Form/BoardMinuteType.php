<?php

namespace App\Form;

use App\Entity\BoardMinute;
use App\Helpers\Year;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class BoardMinuteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category', ChoiceType::class, [
                'label' => 'Choisir la catégorie',
                'mapped' => false,
                'choices' => [
                    '' => '',
                    'Commune' => 'commune',
                    'Département' => 'department',
                    'Région' => 'area'
                ],
                'attr' => [
                    'class' => 'select-js',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Ce champs est requis'])
                ]
            ])
            ->add('target', ChoiceType::class, [
                'label' => 'Nom (Commune, Département ou Région)',
                'disabled' => true,
                'mapped' => false,
                'constraints' => [
                    new NotBlank(['message' => 'Ce champs est requis'])
                ]
            ])
            ->add('targetCode', null, [
                'label' => 'Code INSEE Commune',
                'disabled' => true,
                'mapped' => false
            ])
            ->add('title', null, [
                'label' => 'Titre du document'
            ])
            ->add('month', ChoiceType::class, [
                'placeholder' => '-- Choisir --',
                'label' => 'Mois',
                'choices' => [
                    'Janvier' =>'janvier',
                    'Février' => 'février',
                    'Mars' => 'mars',
                    'Avril' => 'avril',
                    'Mai' => 'mai',
                    'Juin' => 'juin',
                    'Juillet' => 'juillet',
                    'Aôut' => 'aôut',
                    'Septembre' => 'septembre',
                    'Octobre' => 'octobre',
                    'Novembre' => 'novembre',
                    'Décembre' => 'décembre',
                ]
            ])
            ->add('year', ChoiceType::class, [
                'choice_loader' => new CallbackChoiceLoader(function () {
                    return Year::getYears(30);
                }),
                'placeholder' => '-- Choisir --',
                'label' => 'Année'
            ])
            ->add('files', FileType::class, [
                'label' => false,
                'mapped' => false,
                'multiple' => true,
                'attr' => [
                    'accept' => '.pdf'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary btn-block'
                ],
                'label' => '<i class="fa fa-save"></i> Enregistrer',
                'label_html' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => BoardMinute::class,
        ]);
    }
}
