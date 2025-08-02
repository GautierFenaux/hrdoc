<?php

namespace App\Controller\Admin;

use App\Entity\Manager;
use App\Repository\UserRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ManagerCrudController extends AbstractCrudController
{
    public function __construct(private UserRepository $userRepository) {}

    public static function getEntityFqcn(): string
    {
        return Manager::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions;
    }


    
    public function configureFields(string $pageName): iterable
    {
        $userName = [];
        $userMail = [];
        foreach ($this->userRepository->findAll() as $key => $user) {
            $userMail[$user->getEmail()] = $user->getEmail();
            $userName[$user->getName().' '.$user->getSurname()] = $user->getName().' '.$user->getSurname();
        }

        return [
            yield ChoiceField::new('name', 'Manager')->setChoices($userName),
            yield ChoiceField::new('email')->setChoices($userMail),
            yield TextField::new('departement', 'Département'),
            yield AssociationField::new('users', 'Collaborateurs')->setFormTypeOptions([
                'by_reference' => false,
            ])->autocomplete()
        ];
    }
    
    
}
