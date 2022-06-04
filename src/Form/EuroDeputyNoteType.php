<?php

namespace App\Form;

use App\Entity\EuroDeputyNote;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EuroDeputyNoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('physicalPresence', HiddenType::class, [
                'label' => 'Présence physique pendant les séances plénières'
            ])
            ->add('amendmentsNumber', HiddenType::class, [
                'label' => 'Nombre d\'amendements'
            ])
            ->add('votesNumber', HiddenType::class, [
                'label' => 'Nombre de votes'
            ])
            ->add('participationsNumber', HiddenType::class, [
                'label' => 'Nombre de contributions aux débats en séance plénière'
            ])
            ->add('suggestionsNumber', HiddenType::class, [
                'label' => 'Nombre de propositions de résolution'
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
            'data_class' => EuroDeputyNote::class,
        ]);
    }
}
