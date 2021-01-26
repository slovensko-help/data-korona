<?php

namespace App\Controller\Admin;

use App\Entity\NcziMorningEmail;
use DateTimeImmutable;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;

class NcziMorningEmailCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return NcziMorningEmail::class;
    }

    public function createIndexQueryBuilder(
        SearchDto $searchDto,
        EntityDto $entityDto,
        FieldCollection $fields,
        FilterCollection $filters
    ): QueryBuilder
    {
        return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters)
            ->addOrderBy('entity.publishedOn', 'DESC');
    }

    public function configureFields(string $pageName): iterable
    {
        if ('edit' === $pageName || 'new' === $pageName) {
            $fields = [];

            /** @var Field $field */
            foreach (parent::configureFields($pageName) as $field) {
                switch ($field->getAsDto()->getProperty()) {
                    case 'isManuallyOverridden':
                        $field->setHelp('isManuallyOverriddenHelp');
                        break;
                    case 'publishedOn':
                    case 'reportedAt':
                        $field
                            ->setCustomOption(DateTimeField::OPTION_WIDGET, DateTimeField::WIDGET_CHOICE);
                        break;
                    case 'updatedAt':
                    case 'createdAt':
                        $field->hideOnForm();
                }

                $fields[] = $field;
            }

            $priorityFields = [
                'publishedOn' => '010_',
                'isManuallyOverridden' => '020_',
                'slovakiaVaccinationAllDelta' => '030_',
                'slovakiaVaccinationAllTotal' => '030_',
                'slovakiaTestsPcrPositiveDelta' => '040_',
                'slovakiaTestsPcrPositiveDeltaWithoutQuarantine' => '040_',
                'reportedAt' => 'x_',
            ];

            usort($fields, function(FieldInterface $f1, FieldInterface $f2) use ($priorityFields) {
                 $f1Property = $f1->getAsDto()->getProperty();
                 $f2Property = $f2->getAsDto()->getProperty();

                $f1SortKey = ($priorityFields[$f1Property] ?? '') . $f1Property;
                $f2SortKey = ($priorityFields[$f2Property] ?? '') . $f2Property;

                 return $f1SortKey < $f2SortKey ? -1 : ($f1SortKey > $f2SortKey ? 1 : 0);
            });

            foreach ($fields as $field) {
                yield $field;
            }
        }

        if ('index' === $pageName) {
            yield DateField::new('publishedOn', 'publishedOn')
                ->setFormat('d. M. yyyy');
            yield IntegerField::new('slovakiaTestsPcrPositiveDelta');
            yield IntegerField::new('slovakiaTestsAgPositiveDelta');
            yield IntegerField::new('slovakiaVaccinationAllDelta');
            yield IntegerField::new('slovakiaVaccinationAllTotal');
            yield BooleanField::new('isManuallyOverridden');
        }
    }

    public function persistEntity(EntityManagerInterface $entityManager, $ncziMorningEmail): void
    {
        $ncziMorningEmail = $this->updateId($ncziMorningEmail);

        try {
            parent::persistEntity($entityManager, $ncziMorningEmail);
        } /** @noinspection PhpRedundantCatchClauseInspection */ catch (UniqueConstraintViolationException $exception) {
//            $this->addSlugDuplicateFlash();
        }
    }

    public function updateEntity(EntityManagerInterface $entityManager, $ncziMorningEmail): void
    {
        $ncziMorningEmail = $this->updateId($ncziMorningEmail);

        try {
            parent::updateEntity($entityManager, $ncziMorningEmail);
        } /** @noinspection PhpRedundantCatchClauseInspection */ catch (UniqueConstraintViolationException $exception) {
//            $this->addSlugDuplicateFlash();
        }
    }

    public function updateId(NcziMorningEmail $ncziMorningEmail) {
        return $ncziMorningEmail->setId((int) $ncziMorningEmail->getPublishedOn()->format('Ymd'));
    }

    public function createEntity(string $entityFqcn)
    {
        $ncziMorningEmail = new NcziMorningEmail();

        $ncziMorningEmail->setPublishedOn(new DateTimeImmutable('1 day ago'));
        $ncziMorningEmail->setReportedAt(new DateTimeImmutable('1 day ago'));

        return $this->updateId($ncziMorningEmail);
    }
}
