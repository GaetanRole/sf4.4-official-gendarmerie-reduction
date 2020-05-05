<?php

declare(strict_types=1);

namespace App\Controller\Brand;

use \Exception;
use App\Entity\Brand;
use App\Form\BrandType;
use App\Repository\Adapter\RepositoryAdapterInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/admin/brand", name="app_admin_brand_")
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
        return $this->render('brand/admin/index.html.twig', [
            'brands' => $this->repositoryAdapter->getRepository(Brand::class)->findBy([], ['name' => 'ASC']),
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET", "POST"})
     *
     * @throws Exception Datetime Exception
     */
    public function new(Request $request): Response
    {
        $form = $this->createForm(BrandType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->repositoryAdapter->save($form->getData());

            return $this->redirectToRoute('app_admin_brand_index');
        }

        return $this->render('brand/admin/new.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/{uuid<^.{36}$>}/edit", name="edit", methods={"GET", "POST"})
     *
     * @throws Exception Datetime Exception
     */
    public function edit(Request $request, Brand $brand): Response
    {
        $form = $this->createForm(BrandType::class, $brand);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->repositoryAdapter->update($brand);

            return $this->redirectToRoute('app_admin_brand_index');
        }

        return $this->render('brand/admin/edit.html.twig', ['brand' => $brand, 'form' => $form->createView()]);
    }

    /**
     * @Route("/{uuid<^.{36}$>}", name="delete", methods={"DELETE"})
     */
    public function delete(Request $request, Brand $brand, TranslatorInterface $translator): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$brand->getUuid()->toString(), $request->request->get('_token'))) {
            if ($brand->getReductions()->count() > 0) {
                $this->addFlash('danger', $translator->trans('brand.delete.flash.danger', [], 'flashes'));

                return $this->redirectToRoute('app_admin_brand_index');
            }
            $this->repositoryAdapter->delete($brand);
        }

        return $this->redirectToRoute('app_admin_brand_index');
    }
}
