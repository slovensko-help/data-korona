<?php

namespace App\Controller\Admin;

use App\Entity\Raw\HospitalVaccinationSubstitute;
use App\Entity\Raw\NcziMorningEmail;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\String\Slugger\SluggerInterface;

class HospitalVaccinationSubstituteCrudController extends AbstractCrudController
{
    private $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

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

    public function persistEntity(EntityManagerInterface $entityManager, $entity): void
    {
        $entity = $this->updateId($entity);

        try {
            parent::persistEntity($entityManager, $entity);
        } /** @noinspection PhpRedundantCatchClauseInspection */ catch (UniqueConstraintViolationException $exception) {
//            $this->addSlugDuplicateFlash();
        }
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entity): void
    {
        $entity = $this->updateId($entity);

        try {
            parent::updateEntity($entityManager, $entity);
        } /** @noinspection PhpRedundantCatchClauseInspection */ catch (UniqueConstraintViolationException $exception) {
//            $this->addSlugDuplicateFlash();
        }
    }

    public function updateId(HospitalVaccinationSubstitute $entity)
    {
        return $entity->setId($this->slugger
            ->slug($entity->getHospitalName(), '')
            ->lower()
            ->replaceMatches('/[aeiouy]/', '')
            ->slice(0, 100));
    }

    public function createEntity(string $entityFqcn)
    {
        $entity = new HospitalVaccinationSubstitute();
        return $entity->setId('newid');
    }
}
