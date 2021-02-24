<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

/**
 * @IsGranted("ROLE_ADMIN")
 */
class UserCrudController extends AbstractCrudController
{
    private $requestStack;
    private $encoderFactory;

    public function __construct(RequestStack $requestStack, EncoderFactoryInterface $encoderFactory)
    {
        $this->requestStack = $requestStack;
        $this->encoderFactory = $encoderFactory;
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('users')
            ->setEntityLabelInSingular('user')
            ->setPageTitle(Crud::PAGE_EDIT, 'edit_user')
            ->setPageTitle(Crud::PAGE_INDEX, 'user_list')
            ->setPageTitle(Crud::PAGE_NEW, 'create_user')
            ->setSearchFields(['email']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield ChoiceField::new('roles', 'roles')
            ->allowMultipleChoices(true)
            ->setChoices([
                'Admin' => 'ROLE_ADMIN',
                'Editor náhradníkov' => 'ROLE_SUBSTITUTES_EDITOR',
            ])
            ->setFormTypeOption('required', true)
            ->setFormTypeOption('attr.data-allow-clear', 'false')
            ->setSortable(false)
            ->hideOnIndex()
            ->setTemplateName('crud/field/text');

        yield EmailField::new('email', 'email')
            ->setTemplateName('crud/field/text')
            ->setSortable(false);

        yield TextField::new('password', 'password')
            ->onlyOnForms()
            ->setRequired('new' === $pageName)
            ->setFormTypeOption('mapped', false)
            ->setFormType(PasswordType::class)
            ->setHelp('password_help');
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        parent::persistEntity($entityManager, $this->userWithEncodedPassword($entityInstance));
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        parent::updateEntity($entityManager, $this->userWithEncodedPassword($entityInstance));
    }

    private function userWithEncodedPassword(User $user): User
    {
        $plainPassword = $this->requestStack->getMasterRequest()->request->get('User')['password'] ?? null;

        if (!empty($plainPassword)) {
            $user->setPassword(
                $this->encoderFactory->getEncoder(User::class)->encodePassword(
                    $plainPassword,
                    null
                )
            );

            /** @var User $loggedUser */
            $loggedUser = $this->getUser();

            if ($user->getId() === $loggedUser->getId()) {
                $this->addFlash('success', 'password_change_success');
            }
        }

        return $user;
    }
}
