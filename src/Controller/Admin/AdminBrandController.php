<?php

namespace App\Controller\Admin;

use Exception;
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
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @todo    Add patterns on each methods (mediator, adapter...).
 *
 * @Route("/admin/brand", name="app_admin_brand_")
 * @IsGranted("ROLE_ADMIN")
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
class AdminBrandController extends AbstractController
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
    public function index(BrandRepository $brandRepository): Response
    {
        return $this->render('admin/brand/index.html.twig', [
            'brands' => $brandRepository->findBy([], ['name' => 'ASC']),
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET","POST"})
     * @return  RedirectResponse|Response A Response instance
     * @throws  Exception Datetime Exception
     */
    public function new(Request $request, GlobalClock $clock)
    {
        $brand = new Brand();
        $form = $this->createForm(BrandType::class, $brand);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $brand->setCreationDate($clock->getNowInDateTime());
            $this->em->persist($brand);
            $this->em->flush();

            $this->addFlash('success', $this->translator->trans('brand.new.flash.success', [], 'flashes'));
            return $this->redirectToRoute('app_admin_brand_index');
        }

        return $this->render('admin/brand/new.html.twig', ['brand' => $brand, 'form' => $form->createView()]);
    }

    /**
     * @Route("/{id<\d+>}/edit", name="edit", methods={"GET","POST"})
     * @return  RedirectResponse|Response A Response instance
     */
    public function edit(Request $request, Brand $brand)
    {
        $form = $this->createForm(BrandType::class, $brand);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            $this->addFlash('success', $this->translator->trans('brand.edit.flash.success', [], 'flashes'));
            return $this->redirectToRoute('app_admin_brand_index');
        }

        return $this->render('admin/brand/edit.html.twig', ['brand' => $brand, 'form' => $form->createView()]);
    }

    /**
     * @Route("/{id<\d+>}", name="delete", methods={"DELETE"})
     */
    public function delete(Request $request, Brand $brand): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$brand->getId(), $request->request->get('_token'))) {
            if ($brand->getReductions()->count() > 0) {
                $this->addFlash('danger', $this->translator->trans('brand.delete.flash.danger', [], 'flashes'));
                return $this->redirectToRoute('app_admin_brand_index');
            }

            $this->addFlash('success', $this->translator->trans('brand.delete.flash.success', [], 'flashes'));
            $this->em->remove($brand);
            $this->em->flush();
        }

        return $this->redirectToRoute('app_admin_brand_index');
    }
}
