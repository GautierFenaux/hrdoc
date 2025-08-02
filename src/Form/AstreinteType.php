<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Astreinte;
use Doctrine\ORM\QueryBuilder;
use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AstreinteType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $manager =  $options['manager'];
        switch (true) {
            case $options['isEdit']:
                $builder
                    ->add('motifRefusCollab', TextareaType::class, [
                        'attr' => array('style' => 'width: 500px ; height: 50px'),
                        'label' => 'Motif de la réouverture ',
                        'required' => false
                    ])
                    ->add('isOk',  ChoiceType::class, [
                        'choices' => array(
                            'J\'accepte' => true,
                            'Je refuse' => false,
                        ),
                        'expanded' => true,
                        'required' => true,
                        'label' => 'Avis collaborateur : ',
                        'attr' => array('class' => 'radio-wrapper')
                    ]);
                break;
            case $options['isEditRh']:
                $builder->add('motifRefusRh', TextareaType::class, [
                    'attr' => array('style' => 'width: 500px ; height: 50px'),
                    'label' => 'Motif de la réouverture ',
                    'required' => false
                ])
                    ->add('isOkRh',  ChoiceType::class, [
                        'choices' => array(
                            'Favorable' => true,
                            'Défavorable' => false,
                        ),
                        'expanded' => true,
                        'required' => true,
                        'label' => 'Avis RH : ',
                        'attr' => array('class' => 'radio-wrapper')
                    ]);

                break;
            case $options['isEditAfterRhValidation']:
                if ($options['astreinte_days'] !== false) {
                    $builder->add('repos', CheckboxType::class, [
                        'label' => 'J\'atteste avoir bénéficié de 11 heures de repos entre les interventions',
                        'required' => false
                    ]);
                    $choices =  ['0' => '0', '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', '9' => '9', '10' => '10'];
                    for ($i = 0; $i <= intval($options['astreinte_days']); $i++) {
                        for ($j = 1; $j <= 4; $j++) {
                            $builder->add('timeSlot_' . $i + 1 . '_' . $j, TimeType::class, [
                                'input'  => 'string',
                                'widget' => 'single_text',
                                'label' => '',
                                'required' => true,
                                'mapped' => false,
                            ]);
                        }
                        $builder
                            ->add('tempsJour_' . $i, ChoiceType::class, [
                                'choices'  => $choices,
                                'mapped' => false,
                            ])
                            ->add('tempsNuit_' . $i, ChoiceType::class, [
                                'choices'  => $choices,
                                'mapped' => false,
                            ]);
                    }
                }
                break;
            case $options['isEditAfterOperation']:
                $builder->add('tempsValorise', TextareaType::class, [
                    'label' => ' ',
                ])
                    ->add('timeModification', CheckboxType::class, [
                        'label' => 'Réouvrir la demande pour modification des temps',
                        'required' => false,
                        'mapped' => false
                    ])
                    ->add('motifRefusRh', TextareaType::class, [
                        'attr' => array('style' => 'width: 500px ; height: 50px'),
                        'label' => 'Motif de la réouverture ',
                        'required' => false
                    ]);
                    if ($options['data']->isAstreinte()) {
                        for ($i = 0; $i <= intval($options['astreinte_days']); $i++) {
                            $builder->add('increased_' . $i, TextareaType::class, [
                                'mapped' => false,
                                'required' => false,
                            ]);
                        }
                    }

                break;
            default:
                $builder
                    ->add('astreinte', ChoiceType::class, [
                        'label' => 'S\'agit-il d\'une demande d\'astreinte : ',
                        'choices' => array(
                            'Oui' => true,
                            'Non' => false,
                        ),
                        'expanded' => true,
                        'data' => null,
                        'required' => true,
                    ])
                    ->add('debutAstreinte', BirthdayType::class, [
                        'widget' => 'single_text',
                        'html5' => false,
                        'attr' => [
                            'data-flatpickr-target' => 'calendar',
                        ],
                        'format' => 'dd-MM-yyyy',
                        'label' => 'Date de début',
                    ])
                    ->add('finAstreinte', BirthdayType::class, [
                        'widget' => 'single_text',
                        'html5' => false,
                        'attr' => [
                            'data-flatpickr-target' => 'calendar',
                        ],
                        'format' => 'dd-MM-yyyy',
                        'label' => 'Date de fin',
                    ])
                    ->add('user', EntityType::class, [
                        'class' => User::class,
                        'label' => 'Collaborateur',
                        'query_builder' => function (UserRepository $userRepository) use ($manager): QueryBuilder {
                            $result =  $userRepository->createQueryBuilder('u')
                                ->andWhere('u.manager = :val')
                                ->setParameter('val', $manager)
                                ->orderBy('u.surname', 'ASC');
                            return $result;
                        },
                        'choice_label' => function ($user) {
                            return $user->getName() . ' ' . $user->getSurname();
                        }
                    ])
                    ->add('motif', TextareaType::class, [
                        'attr' => array('style' => 'width: 500px ; height: 50px')
                    ]);
                break;
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Astreinte::class,
            'manager' => null,
            'isEdit' => false,
            'isEditRh' => false,
            'isEditAfterRhValidation' => false,
            'isEditAfterOperation' => false,
            'astreinte_days' => false,
        ]);
    }
}
