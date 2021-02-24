<?php

namespace App\Controller\Admin;

use App\Entity\Raw\HospitalVaccinationSubstitute;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class HospitalVaccinationSubstituteCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return HospitalVaccinationSubstitute::class;
    }

    public function configureFields(string $pageName): iterable
    {
        if ('edit' === $pageName || 'new' === $pageName) {
            yield TextField::new('hospitalName', 'hospitalName');
            yield AssociationField::new('hospital', 'hospital');
            yield TextField::new('regionName', 'regionName');
            yield TextField::new('cityName', 'cityName');
            yield TextField::new('email', 'email');
            yield TextField::new('link', 'link');
            yield TextareaField::new('phones', 'phones');
            yield TextareaField::new('note', 'note');
        }

        if ('index' === $pageName) {
            yield TextField::new('hospitalName', 'hospitalName');
            yield AssociationField::new('hospital', 'hospital');
            yield TextField::new('cityName', 'cityName');
            yield TextField::new('regionName', 'regionName');
        }
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPaginatorPageSize(100);
    }
}
