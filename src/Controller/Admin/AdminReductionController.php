<?php

namespace App\Controller\Admin;

use Exception;
use App\Entity\Reduction;
use App\Form\ReductionType;
use EasySlugger\SluggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ModelAdapter\EntityRepositoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Contracts\Translation\TranslatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @todo    Add mediator pattern.
 *
 * @Route("/admin/reduction", name="app_admin_reduction_")
 * @IsGranted("ROLE_ADMIN")
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
class AdminReductionController extends AbstractController
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
     * @Route("/{slug}/edit", name="edit", methods={"GET","POST"})
     * @return  RedirectResponse|Response A Response instance
     * @throws  Exception Datetime Exception
     */
    public function edit(Request $request, Reduction $reduction, SluggerInterface $slugger)
    {
        $form = $this->createForm(ReductionType::class, $reduction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reduction->setSlug($slugger->uniqueSlugify($reduction->getTitle()));

            $this->entityRepository->update($reduction);
            $this->addFlash('success', $this->translator->trans('reduction.edit.flash.success', [], 'flashes'));
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
        if ($this->isCsrfTokenValid('delete'.$reduction->getId(), $request->request->get('_token'))) {
            $this->entityRepository->delete($reduction);
            $this->addFlash('success', $this->translator->trans('reduction.delete.flash.success', [], 'flashes'));
        }

        return $this->redirectToRoute('app_reduction_index');
    }
}
