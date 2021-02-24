<?php

namespace App\Controller\Admin;

use App\Entity\Raw\HospitalVaccinationSubstitute;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("ROLE_SUBSTITUTES_EDITOR")
 */
class HospitalVaccinationSubstituteCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return HospitalVaccinationSubstitute::class;
    }

    public function configureFields(string $pageName): iterable
    {
        if ('edit' === $pageName || 'new' === $pageName) {
            yield AssociationField::new('hospital', 'hospital');
            yield TextField::new('email', 'substitutesEmail');
            yield TextField::new('link', 'substitutesLink');
            yield TextareaField::new('phones', 'substitutesPhones')
                ->setHelp('Jedno telefónne číslo na jeden riadok.');
            yield TextareaField::new('note', 'substitutesNote')
                ->setHelp('Napríklad čas, kedy je možné telefonovať.');
            yield TextField::new('hospitalName', 'hospitalName')
                ->setHelp('Slúži len ako interná poznámka v tejto administrácii.');
            yield BooleanField::new('isAcceptingNewRegistrations', 'isAcceptingNewRegistrations');
        }

        if ('index' === $pageName) {
            yield AssociationField::new('hospital', 'hospital');
            yield TextField::new('hospitalName', 'hospitalName');
            yield BooleanField::new('isAcceptingNewRegistrations', 'isAcceptingNewRegistrations');
        }
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPaginatorPageSize(100);
    }
}
