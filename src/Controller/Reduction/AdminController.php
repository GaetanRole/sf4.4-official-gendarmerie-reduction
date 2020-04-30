<?php

declare(strict_types=1);

namespace App\Controller\Reduction;

use \Exception;
use App\Entity\Reduction;
use App\Form\ReductionType;
use EasySlugger\SluggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Repository\Adapter\RepositoryAdapterInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin/reduction", name="app_admin_reduction_")
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
     * @Route("/waiting-list", name="waiting_list", methods={"GET"})
     */
    public function list(): Response
    {
        $repository = $this->repositoryAdapter->getRepository(Reduction::class);

        return $this->render('reduction/admin/waiting_list.html.twig', [
            'reductions' => $repository->findBy(['isActive'=> false], ['createdAt' => 'DESC'])
        ]);
    }

    /**
     * @see     ImageUploadListener
     *
     * @Route("/{slug}/edit", name="edit", methods={"GET","POST"})
     * @throws  Exception Datetime Exception
     */
    public function edit(Request $request, Reduction $reduction, SluggerInterface $slugger): Response
    {
        $oldReductionTitle = $reduction->getTitle();

        $form = $this->createForm(ReductionType::class, $reduction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($oldReductionTitle !== $form['title']->getData()) {
                $reduction->setSlug($slugger->uniqueSlugify($reduction->getTitle()));
            }

            $this->repositoryAdapter->update($reduction);
            return $this->redirectToRoute('app_reduction_index');
        }

        return $this->render('reduction/admin/edit.html.twig', [
            'reduction' => $reduction,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{slug}", name="change_status", methods={"PUT"})
     */
    public function changeStatus(Request $request, Reduction $reduction): RedirectResponse
    {
        if ($this->isCsrfTokenValid('status'.$reduction->getSlug(), $request->request->get('_token'))) {
            $reduction->isActive() ? $reduction->setIsActive(false) : $reduction->setIsActive(true);
            $this->repositoryAdapter->update($reduction);
        }

        return $this->redirectToRoute('app_admin_reduction_waiting_list');
    }

    /**
     * @Route("/{slug}", name="delete", methods={"DELETE"})
     */
    public function delete(Request $request, Reduction $reduction): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$reduction->getSlug(), $request->request->get('_token'))) {
            $this->repositoryAdapter->delete($reduction);
        }

        return $this->redirectToRoute('app_reduction_index');
    }
}
