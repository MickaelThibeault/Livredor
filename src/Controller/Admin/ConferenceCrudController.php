<?php

namespace App\Controller\Admin;

use App\Entity\Conference;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;

class ConferenceCrudController extends AbstractCrudController
{

    public const ACTION_DUPLICATE = 'duplicate';
    public static function getEntityFqcn(): string
    {
        return Conference::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $duplicate = Action::new(self::ACTION_DUPLICATE)
        ->linkToCrudAction('duplicateConference')
        ->setCssClass('btn btn-info');

        return $actions
            ->add(Crud::PAGE_EDIT, $duplicate)
            ->reorder(Crud::PAGE_EDIT, [self::ACTION_DUPLICATE, Action::SAVE_AND_RETURN]);
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('city');
        yield TextField::new('year');
        yield BooleanField::new('isInternational');
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */

    public function duplicateConference(
            AdminContext $context,
            EntityManagerInterface $em,
            AdminUrlGenerator $urlGenerator
        ) : Response
    {
        $conference = $context->getEntity()->getInstance();
        $duplicatedConference = clone $conference;
        parent::persistEntity($em, $duplicatedConference);
        $url = $urlGenerator->setController(self::class)
            ->setAction(Action::DETAIL)
            ->setEntityId($duplicatedConference->getId())
            ->generateUrl();

        return $this->redirect($url);
    }

}
