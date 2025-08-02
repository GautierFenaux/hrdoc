<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Manager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use Symfony\Component\Security\Core\User\UserInterface;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class AdminDashboardController extends AbstractDashboardController
{

    public function __construct(private RequestStack $requestStack)
    {}

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $loggedUser = $this->getUser();
        if (!$loggedUser instanceof User || !in_array('ROLE_ADMIN', $loggedUser->getRoles())) {
            throw $this->createAccessDeniedException();
        }

        // return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(UserCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' ===  $this->getUser()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('admin/admin_dahsboard.html.twig');
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        // Usually it's better to call the parent method because that gives you a
        // user menu with some menu items already created ("sign out", "exit impersonation", etc.)
        return parent::configureUserMenu($user)
            // use the given $user object to get the user name
            ->setName( $this->getUser()->getName() .' ' .  $this->getUser()->getSurName() )
            // use this method if you don't want to display the name of the user

            ->setGravatarEmail($this->getUser()->getEmail())

            // you can use any type of menu item, except submenus
            ->addMenuItems([
                MenuItem::linkToRoute('Revenir sur mon espace', 'fa fa-sign-out', 'app_rh_dashboard'),
            ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Hrdoc')
            ->generateRelativeUrls();
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Collaborateurs', 'fa fa-users');
        yield MenuItem::linkToCrud('Manager', 'fa fa-user-gear', Manager::class);
    }
}
