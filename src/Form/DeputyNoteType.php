<?php

namespace App\Form;

use App\Entity\DeputyNote;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeputyNoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('presenceNumber', HiddenType::class, [
                'label' => 'Nombre de présence physique pendant les séances'
            ])
            ->add('amendmentsNumber', HiddenType::class, [
                'label' => 'Nombre d\'amendements'
            ])
            ->add('votesNumber', HiddenType::class, [
                'label' => 'Nombre de votes'
            ])
            ->add('participationsNumber', HiddenType::class, [
                'label' => 'Nombre de participation aux travaux parlementaires'
            ])
            ->add('suggestionsNumber', HiddenType::class, [
                'label' => 'Nombre de propositions de loi'
            ])
            ->add('reportsNumber', HiddenType::class, [
                'label' => 'Nombre de rapports législatifs'
            ])
            ->add('questionsNumber', HiddenType::class, [
                    'label' => 'Nombre de questions écrites et orales'
            ])
            ->add('comment', null, [
                'label' => 'Commentaire'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => DeputyNote::class,
        ]);
    }
}
