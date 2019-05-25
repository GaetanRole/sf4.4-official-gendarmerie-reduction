<?php

namespace App\Controller\Admin;

use App\Entity\Reduction;
use App\Form\ReductionType;
use EasySlugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @todo    Add patterns on each methods (mediator, adapter...).
 *
 * @Route("/admin/reduction", name="app_admin_reduction_")
 * @IsGranted("ROLE_ADMIN")
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
class AdminReductionController extends AbstractController
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
     * @Route("/{slug}/edit", name="edit", methods={"GET","POST"})
     * @return  RedirectResponse|Response A Response instance
     */
    public function edit(Request $request, Reduction $reduction, SluggerInterface $slugger)
    {
        $form = $this->createForm(ReductionType::class, $reduction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reduction->setSlug($slugger->uniqueSlugify($reduction->getTitle()));
            $this->em->flush();

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
            $this->em->remove($reduction);
            $this->em->flush();

            $this->addFlash('success', $this->translator->trans('reduction.delete.flash.success', [], 'flashes'));
        }

        return $this->redirectToRoute('app_reduction_index');
    }
}
