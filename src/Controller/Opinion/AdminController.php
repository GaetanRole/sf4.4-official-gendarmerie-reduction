<?php

declare(strict_types=1);

namespace App\Controller\Opinion;

use \Exception;
use App\Entity\Opinion;
use App\Form\OpinionType;
use App\Repository\Adapter\RepositoryAdapterInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/opinion", name="app_admin_opinion_")
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
     * @todo    Probably have to add a dynamic edit below a Reduction.
     *
     * @Route("/{uuid<^.{36}$>}/edit", name="edit", methods={"GET", "POST"})
     *
     * @throws Exception Datetime Exception
     */
    public function edit(Request $request, Opinion $opinion): Response
    {
        $form = $this->createForm(OpinionType::class, $opinion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->repositoryAdapter->update($opinion);

            return $this->redirectToRoute('app_admin_dashboard');
        }

        return $this->render('opinion/admin/edit.html.twig', ['opinion' => $opinion, 'form' => $form->createView()]);
    }

    /**
     * @Route("/{uuid<^.{36}$>}", name="delete", methods="DELETE")
     */
    public function delete(Request $request, Opinion $opinion): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$opinion->getUuid()->toString(), $request->request->get('_token'))) {
            $this->repositoryAdapter->delete($opinion);
        }

        return $this->redirectToRoute('app_admin_dashboard');
    }
}
