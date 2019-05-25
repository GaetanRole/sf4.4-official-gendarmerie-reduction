<?php

namespace App\Controller\Admin;

use Exception;
use App\Entity\Category;
use App\Form\CategoryType;
use App\Service\GlobalClock;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @todo    Add patterns on each methods (mediator, adapter...).
 *
 * @Route("/admin/category", name="app_admin_category_")
 * @IsGranted("ROLE_ADMIN")
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
class AdminCategoryController extends AbstractController
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var TranslatorInterface */
    private $translator;

    public function __construct(EntityManagerInterface $em, TranslatorInterface $translator)
    {
        $this->em = $em;
        $this->translator = $translator;
    }

    /**
     * @todo    Add paginator PagerFanta.
     *
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('admin/category/index.html.twig', [
            'categories' => $categoryRepository->findBy([], ['name' => 'ASC']),
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET","POST"})
     * @return  RedirectResponse|Response A Response instance
     * @throws  Exception Datetime Exception
     */
    public function new(Request $request, GlobalClock $clock)
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category->setCreationDate($clock->getNowInDateTime());
            $this->em->persist($category);
            $this->em->flush();

            $this->addFlash('success', $this->translator->trans('category.new.flash.success', [], 'flashes'));
            return $this->redirectToRoute('app_admin_category_index');
        }

        return $this->render('admin/category/new.html.twig', ['category' => $category, 'form' => $form->createView()]);
    }

    /**
     * @Route("/{id<\d+>}/edit", name="edit", methods={"GET","POST"})
     * @return  RedirectResponse|Response A Response instance
     */
    public function edit(Request $request, Category $category)
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            $this->addFlash('success', $this->translator->trans('category.edit.flash.success', [], 'flashes'));
            return $this->redirectToRoute('app_admin_category_index');
        }

        return $this->render('admin/category/edit.html.twig', ['category' => $category, 'form' => $form->createView()]);
    }

    /**
     * @Route("/{id<\d+>}", name="delete", methods={"DELETE"})
     */
    public function delete(Request $request, Category $category): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            if ($category->getReductions()->count() > 0) {
                $this->addFlash('danger', $this->translator->trans('category.delete.flash.danger', [], 'flashes'));
                return $this->redirectToRoute('app_admin_category_index');
            }

            $this->addFlash('success', $this->translator->trans('category.delete.flash.success', [], 'flashes'));
            $this->em->remove($category);
            $this->em->flush();
        }

        return $this->redirectToRoute('app_admin_category_index');
    }
}
