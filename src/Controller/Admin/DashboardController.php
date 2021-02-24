<?php

namespace App\Controller\Admin;

use App\Entity\Raw\HospitalVaccinationSubstitute;
use App\Entity\Raw\NcziMorningEmail;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\CrudUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class DashboardController extends AbstractDashboardController
{
    private $crudUrlGenerator;
    private $translator;

    public function __construct(
        CrudUrlGenerator $crudUrlGenerator,
        TranslatorInterface $translator
    )
    {
        $this->crudUrlGenerator = $crudUrlGenerator;
        $this->translator = $translator;
    }

    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return $this->redirect(
            $this->crudUrlGenerator
                ->build([
                    'menuIndex' => 0
                ])
                ->setController($this->isGranted('ROLE_ADMIN') ? NcziMorningEmailCrudController::class : HospitalVaccinationSubstituteCrudController::class)
                ->generateUrl()
        );
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Data Korona Devel');
    }

    public function configureMenuItems(): iterable
    {
//        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Ranné emaily z NCZI', 'fa fa-home', NcziMorningEmail::class)
            ->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Náhradníci', 'fa fa-edit', HospitalVaccinationSubstitute::class)
            ->setPermission('ROLE_SUBSTITUTES_EDITOR');
        yield MenuItem::linkToCrud('Administrátori', 'fa fa-user', User::class)
            ->setPermission('ROLE_ADMIN');
        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
    }
}
