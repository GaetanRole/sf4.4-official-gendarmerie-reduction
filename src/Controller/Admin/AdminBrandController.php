<?php

/**
 * AdminBrand Controller File
 *
 * PHP Version 7.2
 *
 * @category    Brand
 * @package     App\Controller\Admin
 * @version     1.0
 * @author      Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */

namespace App\Controller\Admin;

use App\Entity\Brand;
use App\Form\BrandType;
use App\Repository\BrandRepository;
use App\Service\GlobalClock;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * AdminBrand Controller Class
 *
 * @todo Add patterns on each methods (mediator, adapter...)
 *
 * @category    Brand
 * @package     App\Controller\Admin
 * @author      Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 *
 * @Route("/admin/brand")
 * @IsGranted("ROLE_ADMIN")
 */
class AdminBrandController extends AbstractController
{
    /**
     * AdminBrand home page listing all brands
     *
     * @todo Add paginator PagerFanta
     *
     * @param BrandRepository $brandRepository Brand manager
     *
     * @Route("/", name="admin_brand_index", methods={"GET"})
     * @return     Response A Response instance
     */
    public function index(BrandRepository $brandRepository): Response
    {
        return $this->render('admin/brand/index.html.twig', [
            'brands' => $brandRepository->findBy([], ['name' => 'ASC']),
        ]);
    }

    /**
     * Adding one Brand
     *
     * @link https://github.com/Innmind/TimeContinuum Global clock
     * @param Request $request POST'ed data
     * @param EntityManagerInterface $em Entity Manager
     * @param GlobalClock $clock Global project's clock
     *
     * @Route("/new", name="admin_brand_new", methods={"GET","POST"})
     * @return RedirectResponse|Response A Response instance
     * @throws \Exception Datetime Exception
     */
    public function new(
        Request $request,
        EntityManagerInterface $em,
        GlobalClock $clock
    ): Response {
        $brand = new Brand();
        $form = $this->createForm(BrandType::class, $brand);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $brand->setCreationDate($clock->getNowInDateTime());
            $em->persist($brand);
            $em->flush();

            $this->addFlash('success', 'L\'enseigne a bien été ajoutée.');

            return $this->redirectToRoute('admin_brand_index');
        }

        return $this->render('admin/brand/new.html.twig', [
            'brand' => $brand,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Displays a form to edit a existing Brand entity
     *
     * @param Request $request POST'ed data
     * @param Brand $brand Brand given by an id
     * @param EntityManagerInterface $em Entity Manager
     *
     * @Route("/{id<\d+>}/edit", name="admin_brand_edit", methods={"GET","POST"})
     * @return RedirectResponse|Response A Response instance
     */
    public function edit(
        Request $request,
        Brand $brand,
        EntityManagerInterface $em
    ): Response {
        $form = $this->createForm(BrandType::class, $brand);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash(
                'success',
                'L\'enseigne a bien été éditée.'
            );

            return $this->redirectToRoute('admin_brand_index', [
                'id' => $brand->getId(),
            ]);
        }

        return $this->render('admin/brand/edit.html.twig', [
            'brand' => $brand,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Deletes a Brand object
     *
     * @param Request $request POST'ed data
     * @param Brand $brand Brand given by an id
     * @param EntityManagerInterface $em Entity Manager
     *
     * @Route("/{id<\d+>}", name="admin_brand_delete", methods={"DELETE"})
     * @return RedirectResponse A Response instance
     */
    public function delete(
        Request $request,
        Brand $brand,
        EntityManagerInterface $em
    ): RedirectResponse {
        if ($this->isCsrfTokenValid('delete'.$brand->getId(), $request->request->get('_token'))) {
            if ($brand->getReductions()->count() > 0) {
                $this->addFlash(
                    'danger',
                    'L\'enseigne ne peut pas être supprimée si elle est déjà liée à des articles.'
                );
                return $this->redirectToRoute('admin_brand_index');
            }

            $this->addFlash(
                'success',
                'L\'enseigne a bien été supprimée.'
            );

            $em->remove($brand);
            $em->flush();
        }

        return $this->redirectToRoute('admin_brand_index');
    }
}
