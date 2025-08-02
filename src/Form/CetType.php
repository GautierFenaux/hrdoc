<?php

namespace App\Form;

use App\Entity\Cet;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class CetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nbJours', NumberType::class, [
                "required" => false,
                'label' => 'Nombre de jours dans le CET à la date de la demande ',
            ])
            ->add('nbJoursADebiter', NumberType::class, [
                "required" => false,
                'label' => 'Nombre de jours de congé à débiter du CET (10 jours minimum)',
            ])
            ->add('priseCetDebut', BirthdayType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'attr' => [
                    'class' => 'flatpicker',
                ],
                'format' => 'dd-MM-yyyy',
                'label' => 'Date de début de la prise du CET : ',
                'required' => false
            ])
            ->add('priseCetFin', BirthdayType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'attr' => [
                    'class' => 'flatpicker',
                ],
                'format' => 'dd-MM-yyyy',
                'label' => 'Date de fin de la prise du CET : ',
                'required' => false
            ])
            ->add('droitCongesCumule', NumberType::class, [
                "required" => false,

                'label' => 'Droit à congés cumulé (en jours)',
            ])
            ->add('nbJoursCongesUtilises', NumberType::class, [
                "required" => false,
                'label' => 'Nombre de jours de congés utilisés',
            ])
            ->add('soldeJoursCongesNonPris', NumberType::class, [
                "required" => false,
                'label' => 'Solde de jours de congés non pris',
            ])
            ->add('nbJoursVersement', NumberType::class, [
                "required" => false,

                'label' => 'Nombre de jours de congés dont le versement au CET est demandé',
            ])
            ->add('nbJoursLiquide', NumberType::class, [
                'label' => 'Nombre de jours',
                "required" => false,

            ])
            ->add('alimentation', CheckboxType::class, [
                "required" => false,
                "attr" => ['class' => 'cet-choice'],
                'data' => null,
                'label' => 'Alimentation'
            ])
            ->add('restitution',  CheckboxType::class, [
                "required" => false,
                "attr" => ['class' => 'cet-choice'],

                'data' => null,
                'label' => 'Restitution'
            ])
            ->add('utilisation', CheckboxType::class, [
                "required" => false,
                "attr" => ['class' => 'cet-choice'],
                'data' => null,
                'label' => 'Utilisation'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cet::class,
        ]);
    }
}
