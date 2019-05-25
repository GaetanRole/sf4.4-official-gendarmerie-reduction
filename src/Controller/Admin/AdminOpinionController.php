<?php

namespace App\Controller\Admin;

use App\Entity\Opinion;
use App\Form\OpinionType;
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
 * @Route("/admin/opinion", name="app_admin_opinion_")
 * @IsGranted("ROLE_ADMIN")
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
class AdminOpinionController extends AbstractController
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
     * @todo    Probably have to add a dynamic edit below a Reduction.
     *
     * @Route("/{id<\d+>}/edit", name="edit", methods={"GET","POST"})
     * @return  RedirectResponse|Response A Response instance
     */
    public function edit(Request $request, Opinion $opinion)
    {
        $form = $this->createForm(OpinionType::class, $opinion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            $this->addFlash('success', $this->translator->trans('opinion.edit.flash.success', [], 'flashes'));
            return $this->redirectToRoute('app_admin_index');
        }

        return $this->render('admin/opinion/edit.html.twig', ['opinion' => $opinion, 'form' => $form->createView()]);
    }

    /**
     * @Route("/{id<\d+>}", name="delete", methods={"DELETE"})
     */
    public function delete(Request $request, Opinion $opinion): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$opinion->getId(), $request->request->get('_token'))) {
            $this->em->remove($opinion);
            $this->em->flush();

            $this->addFlash('success', $this->translator->trans('opinion.delete.flash.success', [], 'flashes'));
        }

        return $this->redirectToRoute('app_admin_index');
    }
}
