<?php

/**
 * AdminCategory Controller File
 *
 * PHP Version 7.2
 *
 * @category    Category
 * @package     App\Controller\Admin
 * @version     1.0
 * @author      Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */

namespace App\Controller\Admin;

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

/**
 * AdminCategory Controller Class
 *
 * @todo Add patterns on each methods (mediator, adapter...)
 *
 * @category    Category
 * @package     App\Controller\Admin
 * @author      Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 *
 * @Route("/admin/category")
 * @IsGranted("ROLE_ADMIN")
 */
class AdminCategoryController extends AbstractController
{
    /**
     * AdminCategory home page listing all categories
     *
     * @todo Add paginator PagerFanta
     *
     * @param CategoryRepository $categoryRepository Category manager
     *
     * @Route("/", name="admin_category_index", methods={"GET"})
     * @return     Response A Response instance
     */
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('admin/category/index.html.twig', [
            'categories' => $categoryRepository->findBy([], ['name' => 'ASC']),
        ]);
    }

    /**
     * Adding one Category
     *
     * @link https://github.com/Innmind/TimeContinuum Global clock
     * @param Request $request POST'ed data
     * @param EntityManagerInterface $em Entity Manager
     * @param GlobalClock $clock Global project's clock
     *
     * @Route("/new", name="admin_category_new", methods={"GET","POST"})
     * @return RedirectResponse|Response A Response instance
     * @throws \Exception Datetime Exception
     */
    public function new(
        Request $request,
        EntityManagerInterface $em,
        GlobalClock $clock
    ): Response {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category->setCreationDate($clock->getNowInDateTime());
            $em->persist($category);
            $em->flush();

            $this->addFlash('success', 'La catégorie a bien été ajoutée.');

            return $this->redirectToRoute('admin_category_index');
        }

        return $this->render('admin/category/new.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Displays a form to edit a existing Category entity
     *
     * @param Request $request POST'ed data
     * @param Category $category Category given by an id
     * @param EntityManagerInterface $em Entity Manager
     *
     * @Route("/{id<\d+>}/edit", name="admin_category_edit", methods={"GET","POST"})
     * @return RedirectResponse|Response A Response instance
     */
    public function edit(
        Request $request,
        Category $category,
        EntityManagerInterface $em
    ): Response {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash(
                'success',
                'La catégorie a bien été éditée.'
            );

            return $this->redirectToRoute('admin_category_index', [
                'id' => $category->getId(),
            ]);
        }

        return $this->render('admin/category/edit.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Deletes a Category object
     *
     * @param Request $request POST'ed data
     * @param Category $category Category given by an id
     * @param EntityManagerInterface $em Entity Manager
     *
     * @Route("/{id<\d+>}", name="admin_category_delete", methods={"DELETE"})
     * @return RedirectResponse A Response instance
     */
    public function delete(
        Request $request,
        Category $category,
        EntityManagerInterface $em
    ): RedirectResponse {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            if ($category->getReductions()->count() > 0) {
                $this->addFlash(
                    'danger',
                    'La catégorie ne peut pas être supprimée si elle est déjà liée à des articles.'
                );
                return $this->redirectToRoute('admin_category_index');
            }

            $this->addFlash(
                'success',
                'La catégorie a bien été supprimée.'
            );

            $em->remove($category);
            $em->flush();
        }

        return $this->redirectToRoute('admin_category_index');
    }
}
