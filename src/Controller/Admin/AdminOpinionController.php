<?php

namespace App\Controller\Admin;

use Exception;
use App\Entity\Opinion;
use App\Form\OpinionType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Repository\ModelAdapter\EntityRepositoryInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin/opinion", name="app_admin_opinion_")
 * @IsGranted("ROLE_ADMIN")
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
class AdminOpinionController extends AbstractController
{
    /** @var EntityRepositoryInterface */
    private $entityRepository;

    public function __construct(EntityRepositoryInterface $entityRepository)
    {
        $this->entityRepository = $entityRepository;
    }

    /**
     * @todo    Probably have to add a dynamic edit below a Reduction.
     *
     * @Route("/{uuid<^.{36}$>}/edit", name="edit", methods={"GET","POST"})
     * @return  RedirectResponse|Response A Response instance
     * @throws  Exception Datetime Exception
     */
    public function edit(Request $request, Opinion $opinion)
    {
        $form = $this->createForm(OpinionType::class, $opinion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityRepository->update($opinion);
            return $this->redirectToRoute('app_admin_index');
        }

        return $this->render('admin/opinion/edit.html.twig', ['opinion' => $opinion, 'form' => $form->createView()]);
    }

    /**
     * @Route("/{uuid<^.{36}$>}", name="delete", methods={"DELETE"})
     */
    public function delete(Request $request, Opinion $opinion): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$opinion->getId(), $request->request->get('_token'))) {
            $this->entityRepository->delete($opinion);
        }

        return $this->redirectToRoute('app_admin_index');
    }
}
