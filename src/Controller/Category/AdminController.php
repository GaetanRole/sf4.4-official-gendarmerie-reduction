<?php

declare(strict_types=1);

namespace App\Controller\Category;

use \Exception;
use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\Adapter\RepositoryAdapterInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/admin/category", name="app_admin_category_")
 * @IsGranted("ROLE_ADMIN")
 *
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class AdminController extends AbstractController
{
    /** @var RepositoryAdapterInterface */
    private $repositoryAdapter;

    public function __construct(RepositoryAdapterInterface $repositoryAdapter)
    {
        $this->repositoryAdapter = $repositoryAdapter;
    }

    /**
     * @todo    Add paginator PagerFanta.
     *
     * @Route(name="index", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('category/admin/index.html.twig', [
            'categories' => $this->repositoryAdapter->getRepository(Category::class)->findBy([], ['name' => 'ASC']),
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET", "POST"})
     *
     * @throws Exception Datetime Exception
     */
    public function new(Request $request): Response
    {
        $form = $this->createForm(CategoryType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->repositoryAdapter->save($form->getData());

            return $this->redirectToRoute('app_admin_category_index');
        }

        return $this->render('category/admin/new.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/{uuid<^.{36}$>}/edit", name="edit", methods={"GET", "POST"})
     *
     * @throws Exception Datetime Exception
     */
    public function edit(Request $request, Category $category): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->repositoryAdapter->update($category);

            return $this->redirectToRoute('app_admin_category_index');
        }

        return $this->render('category/admin/edit.html.twig', ['category' => $category, 'form' => $form->createView()]);
    }

    /**
     * @Route("/{uuid<^.{36}$>}", name="delete", methods={"DELETE"})
     */
    public function delete(Request $request, Category $category, TranslatorInterface $translator): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$category->getUuid()->toString(), $request->request->get('_token'))) {
            if ($category->getReductions()->count() > 0) {
                $this->addFlash('danger', $translator->trans('category.delete.flash.danger', [], 'flashes'));

                return $this->redirectToRoute('app_admin_category_index');
            }
            $this->repositoryAdapter->delete($category);
        }

        return $this->redirectToRoute('app_admin_category_index');
    }
}
