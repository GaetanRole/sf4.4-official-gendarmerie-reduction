<?php

namespace App\Controller\Admin;

use Exception;
use App\Entity\Category;
use App\Form\CategoryType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\Adapter\EntityRepositoryInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @todo    Add mediator pattern.
 *
 * @Route("/admin/category", name="app_admin_category_")
 * @IsGranted("ROLE_ADMIN")
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
class AdminCategoryController extends AbstractController
{
    /** @var EntityRepositoryInterface */
    private $entityRepository;

    /** @var TranslatorInterface */
    private $translator;

    public function __construct(EntityRepositoryInterface $entityRepository, TranslatorInterface $translator)
    {
        $this->entityRepository = $entityRepository;
        $this->translator = $translator;
    }

    /**
     * @todo    Add paginator PagerFanta.
     *
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('admin/category/index.html.twig', [
            'categories' => $this->entityRepository->getRepository(Category::class)->findBy([], ['name' => 'ASC']),
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET","POST"})
     * @return  RedirectResponse|Response A Response instance
     * @throws  Exception Datetime Exception
     */
    public function new(Request $request)
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityRepository->save($category);
            $this->addFlash('success', $this->translator->trans('category.new.flash.success', [], 'flashes'));
            return $this->redirectToRoute('app_admin_category_index');
        }

        return $this->render('admin/category/new.html.twig', ['category' => $category, 'form' => $form->createView()]);
    }

    /**
     * @Route("/{uuid<^.{36}$>}/edit", name="edit", methods={"GET","POST"})
     * @return  RedirectResponse|Response A Response instance
     * @throws  Exception Datetime Exception
     */
    public function edit(Request $request, Category $category)
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityRepository->update($category);
            $this->addFlash('success', $this->translator->trans('category.edit.flash.success', [], 'flashes'));
            return $this->redirectToRoute('app_admin_category_index');
        }

        return $this->render('admin/category/edit.html.twig', ['category' => $category, 'form' => $form->createView()]);
    }

    /**
     * @Route("/{uuid<^.{36}$>}", name="delete", methods={"DELETE"})
     */
    public function delete(Request $request, Category $category): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            if ($category->getReductions()->count() > 0) {
                $this->addFlash('danger', $this->translator->trans('category.delete.flash.danger', [], 'flashes'));
                return $this->redirectToRoute('app_admin_category_index');
            }
            $this->entityRepository->delete($category);
            $this->addFlash('success', $this->translator->trans('category.delete.flash.success', [], 'flashes'));
        }

        return $this->redirectToRoute('app_admin_category_index');
    }
}
