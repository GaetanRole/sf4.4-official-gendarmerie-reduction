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
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * AdminBrand Controller Class
 *
 * @todo Add patterns on each methods (mediator, adapter...)
 *
 * @category    Brand
 * @package     App\Controller\Admin
 * @author      Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 *
 * @Route("/{_locale}/admin/brand", defaults={"_locale"="%locale%"})
 * @IsGranted("ROLE_ADMIN")
 */
class AdminBrandController extends AbstractController
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
     * AdminBrandController constructor.
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
     * AdminBrand home page listing all brands
     *
     * @todo Add paginator PagerFanta
     *
     * @param BrandRepository $brandRepository Brand manager
     *
     * @Route("/", methods={"GET"})
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
        $brand = new Brand();
        $form = $this->createForm(BrandType::class, $brand);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $brand->setCreationDate($clock->getNowInDateTime());
            $this->em->persist($brand);
            $this->em->flush();

            $this->addFlash('success', $this->translator->trans('brand.new.flash.success', [], 'flashes'));

            return $this->redirectToRoute('app_admin_adminbrand_index');
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
     *
     * @Route("/{id<\d+>}/edit", methods={"GET","POST"})
     * @return RedirectResponse|Response A Response instance
     */
    public function edit(
        Request $request,
        Brand $brand
    ): Response {
        $form = $this->createForm(BrandType::class, $brand);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            $this->addFlash('success', $this->translator->trans('brand.edit.flash.success', [], 'flashes'));

            return $this->redirectToRoute('app_admin_adminbrand_index', [
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
     *
     * @Route("/{id<\d+>}", methods={"DELETE"})
     * @return RedirectResponse A Response instance
     */
    public function delete(
        Request $request,
        Brand $brand
    ): RedirectResponse {
        if ($this->isCsrfTokenValid('delete'.$brand->getId(), $request->request->get('_token'))) {
            if ($brand->getReductions()->count() > 0) {
                $this->addFlash('danger', $this->translator->trans('brand.delete.flash.danger', [], 'flashes'));
                return $this->redirectToRoute('app_admin_adminbrand_index');
            }

            $this->addFlash('success', $this->translator->trans('brand.delete.flash.success', [], 'flashes'));

            $this->em->remove($brand);
            $this->em->flush();
        }

        return $this->redirectToRoute('app_admin_adminbrand_index');
    }
}
