<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category', ChoiceType::class, [
                'choices' => [
                    '' => '',
                    'Maire' => 9,
                    'Président de département' => 10,
                    'Président de région' => 11,
                    'Député' => 8,
                    'Conseillers municipaux' => 1,
                    'Conseillers départementaux' => 3,
                    'Conseillers régionaux' => 4,
                    'Députés européens' => 6,
                    'Sénateurs' => 7,
                    'Conseillers corse' => 5,
                    'Conseillers communautaires' => 2,
                ]
            ])
            ->add('criteria', null, [
                'attr' => [
                    'placeholder' => 'Sélectionnez d\'abord une catégorie d\'élu',
                    'autocomplete' => 'off'
                ],
                'disabled' => true,
            ])
            ->add('search', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary'
                ],
                'label' => 'Rechercher <i class="fa fa-search"></i>',
                'label_html' => true,
                'disabled' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'method' => Request::METHOD_GET
        ]);
    }
}
