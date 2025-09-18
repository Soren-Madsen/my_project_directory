<?php

namespace App\Controller\Admin;

use App\Entity\Person;
use App\Entity\User;
use App\Controller\Admin\PersonCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Panel de Administración');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::section('Gestión');
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Personas', 'fa fa-user', Person::class);
        yield MenuItem::linkToCrud('Usuarios', 'fa fa-users', User::class);

        yield MenuItem::section('Accesos rápidos');
        yield MenuItem::linkToRoute('Volver a la web', 'fa fa-arrow-left', 'person_list');
        yield MenuItem::linkToLogout('Cerrar sesión', 'fa fa-sign-out');
    }
}
