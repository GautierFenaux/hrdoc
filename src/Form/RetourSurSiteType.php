<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\RetourSurSite;
use Doctrine\ORM\QueryBuilder;
use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;


class RetourSurSiteType extends AbstractType
{
    public function __construct(private UserRepository $userRepository) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $manager = $options['manager'];

        if (!$options['validation']) {
            $builder
                ->add('user', EntityType::class, [

                    'class' => User::class,
                    'label' => 'Collaborateur',
                    'query_builder' => function (UserRepository $userRepository) use ($manager): QueryBuilder {
                        $result =  $userRepository->createQueryBuilder('u')
                            ->leftJoin('u.teletravailForms', 'tf')
                            ->andWhere('u.manager = :val')
                            ->andWhere('tf.user = u.id')
                            ->setParameter('val', $manager)
                            ->orderBy('u.name', 'ASC');
                        return $result;
                    },
                    'choice_label' => function ($user) {
                        return $user->getName() . ' ' . $user->getSurname();
                    }
                ])
                ->add('dateFinTeletravail', BirthdayType::class, [
                    'widget' => 'single_text',
                    'html5' => false,
                    'attr' => [
                        'class' => 'flatpicker',
                    ],
                    'format' => 'dd-MM-yyyy',
                    'label' => 'Date de fin télétravail ',
                ])
                ->add('autonomieInsuffisante', CheckboxType::class, [
                    'label' => 'Autonomie insuffisante',
                    'required' => false,

                ])
                ->add('diminutionProductivite', CheckboxType::class, [
                    'label' => 'Diminution de la productivité observée sur la période ',
                    'required' => false,
                ])
                ->add('ProblemesConnexion', CheckboxType::class, [
                    'label' => ' Problèmes de connexion récurrents ',
                    'required' => false,
                ])
                ->add('collaborateurInjoignable', CheckboxType::class, [
                    'label' => ' Salarié fréquemment injoignable en télétravail ',
                    'required' => false,
                ])
                ->add('desorganiseService', CheckboxType::class, [
                    'label' => 'Désorganisation de l’activité ',
                    'required' => false,
                ])
                ->add('autres', TextareaType::class, [
                    'label' => 'Autre ',
                    'required' => false,
                ]);
        } else {

            $builder
                ->add('entretienRh', ChoiceType::class, [
                    'choices' => [
                        'Oui' => true,
                        'Non' => false,
                    ],
                    'expanded' => true,
                    'multiple' => false,
                    'label' => 'Demande d\'entretien avec les ressources humaines : ',
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RetourSurSite::class,
            'manager' => [],
            'validation' => false
        ]);
    }
}
