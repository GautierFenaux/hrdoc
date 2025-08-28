<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\TeletravailForm;
use App\EventSubscriber\FormSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;


class TeletravailFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $request = $options['request'];

        $builder

            ->add('periodeEssai',  ChoiceType::class, [
                'choices' => array(
                    'Oui' => true,
                    'Non' => false,
                ),
                'expanded' => true,
                'data' => null,
                'required' => true,
                'label' => 'Période d\'essai en cours : ',
                'constraints' => [
                    new NotNull([
                        'message' => 'Veuillez saisir cette donnée.',
                    ]),
                ],
            ])
            ->add('activiteEligible',  ChoiceType::class, [
                'choices' => array(
                    'Oui' => true,
                    'Non' => false,
                ),
                'expanded' => true,
                'data' => null,
                'constraints' => [
                    new NotNull([
                        'message' => 'Veuillez saisir cette donnée.',
                    ]),
                ],
                'label' => 'Activité éligible : '
            ])
            ->add('autonomieSuffisante',  ChoiceType::class, [
                'choices' => array(
                    'Oui' => true,
                    'Non' => false,
                ),
                'expanded' => true,
                'data' => null,
                'constraints' => [
                    new NotNull([
                        'message' => 'Veuillez saisir cette donnée.',
                    ]),
                ],
                'label' => 'Autonomie suffisante : '
            ])
            ->add('conditionsEligibilites',  ChoiceType::class, [
                'choices' => array(
                    'Oui' => true,
                    'Non' => false,
                ),
                'expanded' => true,
                'data' => null,
                'constraints' => [
                    new NotNull([
                        'message' => 'Veuillez saisir cette donnée.',
                    ]),
                ],
                'label' => 'Les conditions d\'éligibilité ? '
            ])
            ->add('conditionsTechMatAdm',  ChoiceType::class, [
                'choices' => array(
                    'Oui' => true,
                    'Non' => false,
                ),
                'expanded' => true,
                'data' => null,
                'constraints' => [
                    new NotNull([
                        'message' => 'Veuillez saisir cette donnée.',
                    ]),
                ],
                'label' => 'Les conditions techniques, matérielles et/ou administrative ?'
            ])
            ->add('desorganiseService',  ChoiceType::class, [
                'choices' => array(
                    'Oui' => true,
                    'Non' => false,
                ),
                'expanded' => true,
                'data' => null,
                'constraints' => [
                    new NotNull([
                        'message' => 'Veuillez saisir cette donnée.',
                    ]),
                ],
                'label' => '
                        La répartition des journées télétravaillées ne désorganise pas l\'activité du service 
                        (nombre de salariés en télétravail dépassant le quota journalier sur les journées souhaitées par le salarié...) et permet une présence sur site suffisante au regard de l\'activité, étant précisé que la Direction recommande 50% de présence ?'
            ])
            ->add('avisManager',  ChoiceType::class, [
                'choices' => array(
                    'Favorable' => true,
                    'Défavorable' => false,
                ),
                'expanded' => true,
                'data' => null,
                // 'attr' => [
                //     'style' => 'display: none;', // This applies to the field container
                // ],
            ])
            ->add('commentaireManager', TextareaType::class, [
                'label'  => 'Si avis défavorable, à préciser : ',
                'required' => false
            ]);
        // ->addEventSubscriber(new FormSubscriber($options['current_user']));









        // if ($request instanceof Request) {
        //     if (strpos($request->getPathInfo(), 'manager')) {
        //         $builder

        //             ->add('periodeEssai',  ChoiceType::class, [
        //                 'choices' => array(
        //                     'Oui' => true,
        //                     'Non' => false,
        //                 ),
        //                 'expanded' => true,
        //                 'data' => null,
        //                 'required' => true,
        //                 'label' => 'Période d\'essai en cours : '
        //             ])
        //             ->add('activiteEligible',  ChoiceType::class, [
        //                 'choices' => array(
        //                     'Oui' => true,
        //                     'Non' => false,
        //                 ),
        //                 'expanded' => true,
        //                 'data' => null,
        //                 'required' => true,
        //                 'label' => 'Activité éligible : '
        //             ])
        //             ->add('autonomieSuffisante',  ChoiceType::class, [
        //                 'choices' => array(
        //                     'Oui' => true,
        //                     'Non' => false,
        //                 ),
        //                 'expanded' => true,
        //                 'data' => null,
        //                 'required' => true,
        //                 'label' => 'Autonomie suffisante : '
        //             ])
        //             ->add('conditionsEligibilites',  ChoiceType::class, [
        //                 'choices' => array(
        //                     'Oui' => true,
        //                     'Non' => false,
        //                 ),
        //                 'expanded' => true,
        //                 'data' => null,
        //                 'required' => true,
        //                 'label' => 'Les conditions d\'éligibilité ? '
        //             ])
        //             ->add('conditionsTechMatAdm',  ChoiceType::class, [
        //                 'choices' => array(
        //                     'Oui' => true,
        //                     'Non' => false,
        //                 ),
        //                 'expanded' => true,
        //                 'data' => null,
        //                 'required' => true,
        //                 'label' => 'Les conditions techniques, matérielles et/ou administrative ?'
        //             ])
        //             ->add('desorganiseService',  ChoiceType::class, [
        //                 'choices' => array(
        //                     'Oui' => true,
        //                     'Non' => false,
        //                 ),
        //                 'expanded' => true,
        //                 'data' => null,
        //                 'required' => true,
        //                 'label' => '
        //                 La répartition des journées télétravaillées ne désorganise pas l\'activité du service 
        //                 (nombre de salariés en télétravail dépassant le quota journalier sur les journées souhaitées par le salarié...) et permet une présence sur site suffisante au regard de l\'activité, étant précisé que la Direction recommande 50% de présence ?'
        //             ])
        //             ->add('avisManager',  ChoiceType::class, [
        //                 'choices' => array(
        //                     'Favorable' => true,
        //                     'Défavorable' => false,
        //                 ),
        //                 'expanded' => true,
        //                 'data' => null,
        //                 'attr' => [
        //                     'style' => 'display: none;', // This applies to the field container
        //                 ],
        //             ])
        //             ->add('commentaireManager', TextareaType::class, [
        //                 'label'  => 'Si avis défavorable, à préciser : ',
        //                 'required' => false
        //             ])
        //             ->addEventSubscriber(new FormSubscriber($options['current_user']));
        //     }

        //     //Formulaire rh
        //     else if (strpos($request->getPathInfo(), 'rh')) {
        //         if (!strpos($request->getPathInfo(), 'show')) {
        //             $builder
        //                 ->add('avisDRH',  ChoiceType::class, [
        //                     'choices' => array(
        //                         'Favorable' => true,
        //                         'Défavorable' => false,
        //                     ),
        //                     'expanded' => true,
        //                     'data' => null,
        //                     'required' => true,
        //                     'attr' => [
        //                         'style' => 'display: none;', // This applies to the field container
        //                     ],
        //                 ]);
        //         }


        //         $builder->add('commentaireDRH', TextareaType::class, [
        //             'label'  => 'Si avis défavorable, à préciser : ',
        //             'required' => false
        //         ])
        //             ->add('signature', HiddenType::class, [
        //                 'mapped' => false,
        //                 'data' => null
        //             ]);
        //     }

        //     // Formulaire collaborateur
        //     else if (strpos($request->getPathInfo(), 'teletravailform/new') || strpos($request->getPathInfo(), 'teletravailform/' . $options['id'] . '/edit')) {
        //         $builder
        //             ->add('natureContrat', ChoiceType::class, [
        //                 'choices'  => [
        //                     'CDI' => 'CDI',
        //                     'CDD supérieur à 6 mois consécutif' => 'CDD supérieur à 6 mois consécutif',
        //                 ],
        //                 'label' => 'Nature du contrat : '
        //             ])
        //             // Ajouter un champs pour la quotité si ni temps partiel ou temps complet
        //             ->add('quotite', ChoiceType::class, [
        //                 'choices'  => [
        //                     'Temps complet' => 'Temps complet',
        //                     'Temps partiel' => 'Temps partiel',
        //                 ],
        //                 'label' => 'Quotité : '

        //             ])
        //             ->add('quotitePersonnel', ChoiceType::class, [
        //                 'choices' => [
        //                     'Oui' => true,
        //                     'Non' => false,
        //                 ],
        //                 'expanded' => true,
        //                 'multiple' => false,
        //                 'data' => null,
        //                 'required' => false,
        //                 'attr' => [
        //                     'style' => 'display: none;',
        //                 ],
        //             ]);
        //         if (strpos($request->getPathInfo(), 'teletravailform/' . $options['id'] . '/edit')) {
        //             $builder->add('connexionInternet',  ChoiceType::class, [
        //                 'choices' => array(
        //                     'Oui' => true,
        //                     'Non' => false,
        //                 ),
        //                 'expanded' => true,
        //                 'required' => true,
        //                 'label' => 'Connexion internet : '

        //             ]);
        //         } else {
        //             $builder->add('connexionInternet',  ChoiceType::class, [
        //                 'choices' => array(
        //                     'Oui' => true,
        //                     'Non' => false,
        //                 ),
        //                 'expanded' => true,
        //                 'required' => true,
        //                 'data' => null,
        //                 'label' => 'Connexion internet : '
        //             ]);
        //         }
        //         // Regarder à quoi correspondent les options
        //         $builder->add('journeesTeletravaillees', ChoiceType::class, [
        //             'choices' => [
        //                 'Lundi' => 'Lundi',
        //                 'Mardi' => 'Mardi',
        //                 'Mercredi' => 'Mercredi',
        //                 'Jeudi' => 'Jeudi',
        //                 'Vendredi' => 'Vendredi',
        //             ],
        //             'expanded' => true,
        //             'multiple' => true,
        //             'label' => 'Choix des journées télétravaillées : ',
        //         ])
        //             ->add('attestationAssurance', FileType::class, [
        //                 'data_class' => null,
        //                 'label' => 'A fournir au format PDF : attestation de votre compagnie d\'assurances confirmant le fait que votre situation de télétravail est bien prise en compte dans le contrat d\'assurance multirisque habitation, et que celui-ci couvre désormais votre présence pendant les journées de travail effectuées à son domicile (joint à la demande) ?',
        //                 'required' => true,
        //                 'attr' => [
        //                     'class' => 'attestation',
        //                 ],
        //                 'constraints' => [
        //                     new File([
        //                         'maxSize' => '4M',
        //                         'mimeTypes' => [
        //                             'application/pdf',
        //                         ],
        //                         'mimeTypesMessage' => "Type de document non valide",
        //                     ])
        //                 ],
        //             ])
        //             ->add('attestationHonneur', CheckboxType::class, [
        //                 'data_class' => null,
        //                 'attr' => [
        //                     'class' => 'attestation',
        //                 ],
        //                 'label' => '
        //                     J\'atteste sur l\'honneur que je dispose d\'un espace de travail adapté au télétravail, respectant les règles d\'hygiène, de sécurité et de bonnes conditions de travail, que mon logement est en conformité avec les installations électriques et que je bénéficie d\'un accès internet haut débit.',
        //                 'required' => true,
        //             ])

        //             ->add('lieuTeletravail', TextareaType::class, [
        //                 'label' => 'Lieu du télétravail (adresse complète conforme à celle présente sur l\'attestation d\'assurance) : ',
        //             ])
        //             ->add('aCompterDu', BirthdayType::class, [
        //                 'widget' => 'single_text',
        //                 'html5' => false,
        //                 'required' => false,
        //                 'attr' => [
        //                     'class' => 'flatpicker',
        //                     'data-flatpickr-target' => 'calendar',
        //                 ],
        //                 'format' => 'dd-MM-yyyy',
        //                 'label' => 'Demande à bénéficier du télétravail régulier à compter du : ',
        //                 'by_reference' => true,
        //             ])
        //             ->add('signature', HiddenType::class, [
        //                 'mapped' => false
        //             ]);
        //     }
        // }
    }

    // permet de configurer les options passées au formulaire
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TeletravailForm::class,
            'user_roles' => [],
            'request' => '',
            'current_user' => User::class,
            'teletravail_form_state' => '',
            'id' => 0,
        ]);
    }
}
