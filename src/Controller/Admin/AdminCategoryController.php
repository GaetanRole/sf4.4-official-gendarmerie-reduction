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
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * AdminCategory Controller Class
 *
 * @todo Add patterns on each methods (mediator, adapter...)
 *
 * @category    Category
 * @package     App\Controller\Admin
 * @author      Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 *
 * @Route("/{_locale}/admin/category", defaults={"_locale"="%locale%"})
 * @IsGranted("ROLE_ADMIN")
 */
class AdminCategoryController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * AdminCategoryController constructor.
     *
     * @param EntityManagerInterface $em Entity Manager injection
     * @param TranslatorInterface $translator Translator injection
     */
    public function __construct(EntityManagerInterface $em, TranslatorInterface $translator)
    {
        $this->em = $em;
        $this->translator = $translator;
    }

    /**
     * AdminCategory home page listing all categories
     *
     * @todo Add paginator PagerFanta
     *
     * @param CategoryRepository $categoryRepository Category manager
     *
     * @Route("/", methods={"GET"})
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
     * @param GlobalClock $clock Global project's clock
     *
     * @Route("/new", methods={"GET","POST"})
     * @return RedirectResponse|Response A Response instance
     * @throws \Exception Datetime Exception
     */
    public function new(
        Request $request,
        GlobalClock $clock
    ): Response {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category->setCreationDate($clock->getNowInDateTime());
            $this->em->persist($category);
            $this->em->flush();

            $this->addFlash('success', $this->translator->trans('category.new.flash.success', [], 'flashes'));

            return $this->redirectToRoute('app_admin_admincategory_index');
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
     *
     * @Route("/{id<\d+>}/edit", methods={"GET","POST"})
     * @return RedirectResponse|Response A Response instance
     */
    public function edit(
        Request $request,
        Category $category
    ): Response {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            $this->addFlash('success', $this->translator->trans('category.edit.flash.success', [], 'flashes'));

            return $this->redirectToRoute('app_admin_admincategory_index', [
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
     *
     * @Route("/{id<\d+>}", methods={"DELETE"})
     * @return RedirectResponse A Response instance
     */
    public function delete(
        Request $request,
        Category $category
    ): RedirectResponse {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            if ($category->getReductions()->count() > 0) {
                $this->addFlash('danger', $this->translator->trans('category.delete.flash.danger', [], 'flashes'));
                return $this->redirectToRoute('app_admin_admincategory_index');
            }

            $this->addFlash('success', $this->translator->trans('category.delete.flash.success', [], 'flashes'));

            $this->em->remove($category);
            $this->em->flush();
        }

        return $this->redirectToRoute('app_admin_admincategory_index');
    }
}
