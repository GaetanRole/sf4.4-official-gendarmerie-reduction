<?php

namespace App\Controller\Admin;

use Exception;
use App\Entity\Brand;
use App\Form\BrandType;
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
 * @Route("/admin/brand", name="app_admin_brand_")
 * @IsGranted("ROLE_ADMIN")
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
class AdminBrandController extends AbstractController
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
        return $this->render('admin/brand/index.html.twig', [
            'brands' => $this->entityRepository->getRepository(Brand::class)->findBy([], ['name' => 'ASC']),
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET","POST"})
     * @return  RedirectResponse|Response A Response instance
     * @throws  Exception Datetime Exception
     */
    public function new(Request $request)
    {
        $brand = new Brand();
        $form = $this->createForm(BrandType::class, $brand);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityRepository->save($brand);
            $this->addFlash('success', $this->translator->trans('brand.new.flash.success', [], 'flashes'));
            return $this->redirectToRoute('app_admin_brand_index');
        }

        return $this->render('admin/brand/new.html.twig', ['brand' => $brand, 'form' => $form->createView()]);
    }

    /**
     * @Route("/{uuid<^.{36}$>}/edit", name="edit", methods={"GET","POST"})
     * @return  RedirectResponse|Response A Response instance
     * @throws  Exception Datetime Exception
     */
    public function edit(Request $request, Brand $brand)
    {
        $form = $this->createForm(BrandType::class, $brand);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityRepository->update($brand);
            $this->addFlash('success', $this->translator->trans('brand.edit.flash.success', [], 'flashes'));
            return $this->redirectToRoute('app_admin_brand_index');
        }

        return $this->render('admin/brand/edit.html.twig', ['brand' => $brand, 'form' => $form->createView()]);
    }

    /**
     * @Route("/{uuid<^.{36}$>}", name="delete", methods={"DELETE"})
     */
    public function delete(Request $request, Brand $brand): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$brand->getId(), $request->request->get('_token'))) {
            if ($brand->getReductions()->count() > 0) {
                $this->addFlash('danger', $this->translator->trans('brand.delete.flash.danger', [], 'flashes'));
                return $this->redirectToRoute('app_admin_brand_index');
            }
            $this->entityRepository->delete($brand);
            $this->addFlash('success', $this->translator->trans('brand.delete.flash.success', [], 'flashes'));
        }

        return $this->redirectToRoute('app_admin_brand_index');
    }
}
