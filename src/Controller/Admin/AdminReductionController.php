<?php

declare(strict_types = 1);

namespace App\Controller\Admin;

use \Exception;
use App\Entity\Reduction;
use App\Form\ReductionType;
use EasySlugger\SluggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Repository\ModelAdapter\EntityRepositoryInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin/reduction", name="app_admin_reduction_")
 * @IsGranted("ROLE_ADMIN")
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class AdminReductionController extends AbstractController
{
    /** @var EntityRepositoryInterface */
    private $entityRepository;

    public function __construct(EntityRepositoryInterface $entityRepository)
    {
        $this->entityRepository = $entityRepository;
    }

    /**
     * @Route("/{slug}/edit", name="edit", methods={"GET","POST"})
     * @return  RedirectResponse|Response A Response instance
     * @throws  Exception Datetime Exception
     */
    public function edit(Request $request, Reduction $reduction, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ReductionType::class, $reduction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reduction->setSlug($slugger->uniqueSlugify($reduction->getTitle()));

            $this->entityRepository->update($reduction);
            return $this->redirectToRoute('app_reduction_index');
        }

        return $this->render('admin/reduction/edit.html.twig', [
            'reduction' => $reduction,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{slug}", name="delete", methods={"DELETE"})
     */
    public function delete(Request $request, Reduction $reduction): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$reduction->getSlug(), $request->request->get('_token'))) {
            $this->entityRepository->delete($reduction);
        }

        return $this->redirectToRoute('app_reduction_index');
    }
}
