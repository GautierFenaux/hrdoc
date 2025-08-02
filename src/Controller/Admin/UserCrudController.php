<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::NEW);
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // set this option if you prefer the page content to span the entire
            // browser width, instead of the default design which sets a max width
            ->setPageTitle('edit', 'Collaborateur')
            ->setPageTitle('index', 'Collaborateurs')
            ->setDefaultSort([
                'surname' => 'ASC',
            ]);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('surname')->setLabel('Nom')->setDisabled(true),
            TextField::new('name')->setLabel('Prénom')->setDisabled(true),
            ArrayField::new('roles')->setLabel('Rôles'),
            // TextField::new('matricule')->setLabel('Matricule'),
            BooleanField::new('actif')->setLabel('Actif'),
            BooleanField::new('eligibleTT')->setLabel('Télétravail'),
            BooleanField::new('eligibleCet')->setLabel('CET'),
            BooleanField::new('forfaitheure')->setLabel('Forfait Heure'),
            AssociationField::new('manager'),
        ];
    }

}
