<?php

namespace App\Form;

use App\Entity\MPDPRNote;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MPDPRNoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('security', HiddenType::class, [
                'label' => 'Sécurité'
            ])
            ->add('socialAction', HiddenType::class, [
                'label' => 'Action sociale et santé'
            ])
            ->add('jobProfessionalInsert', HiddenType::class, [
                'label' => 'Emploi - Insertion professionnelle'
            ])
            ->add('teaching', HiddenType::class, [
                'label' => 'Enseignement'
            ])
            ->add('youthChildhood', HiddenType::class, [
                'label' => 'Enfance - Jeunesse'
            ])
            ->add('sports', HiddenType::class, [
                'label' => 'Sports'
            ])
            ->add('economicalIntervention', HiddenType::class, [
                'label' => 'Interventions dans le domaine économique'
            ])
            ->add('cityPolitics', HiddenType::class, [
                'label' => 'Politique de la ville'
            ])
            ->add('ruralDevelopment', HiddenType::class, [
                'label' => 'Aménagement rural, planification et aménagement du territoire '
            ])
            ->add('accommodation', HiddenType::class, [
                'label' => 'Logement et habitat'
            ])
            ->add('environment', HiddenType::class, [
                'label' => 'Environnement et patrimoine'
            ])
            ->add('garbage', HiddenType::class, [
                'label' => 'Déchets'
            ])
            ->add('telecoms', HiddenType::class, [
                'label' => 'Réseaux cablés et télécommunications'
            ])
            ->add('energy', HiddenType::class, [
                'label' => 'Energie'
            ])
            ->add('transports', HiddenType::class, [
                'label' => 'Transports scolaires et publics'
            ])
            ->add('comment', null, [
                'label' => 'Commentaire'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => MPDPRNote::class,
        ]);
    }
}
