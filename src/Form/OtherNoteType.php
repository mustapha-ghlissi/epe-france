<?php

namespace App\Form;

use App\Entity\OtherNote;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OtherNoteType extends AbstractType
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
            ->add('achievementsNumber', HiddenType::class, [
                'label' => 'Nombre de travaux dirigés'
            ])
            ->add('worksNumber', HiddenType::class, [
                'label' => 'Nombre de projets réalisés'
            ])
            ->add('comment', null, [
                'label' => 'Commentaire'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => OtherNote::class,
        ]);
    }
}
