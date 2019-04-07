<?php

/**
 * AdminReduction Controller File
 *
 * @category    Reduction
 * @author      Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */

namespace App\Controller\Admin;

use App\Entity\Reduction;
use App\Form\ReductionType;
use App\Utils\Slugger;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @todo Add patterns on each methods (mediator, adapter...)
 *
 * @Route("/admin/reduction")
 * @IsGranted("ROLE_ADMIN")
 */
class AdminReductionController extends AbstractController
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
     * AdminReductionController constructor.
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
     * Displays a form to edit a existing Reduction entity
     *
     * @param Request $request POST'ed data
     * @param Reduction $reduction Reduction given by an id
     *
     * @Route("/{slug}/edit", methods={"GET","POST"})
     * @return RedirectResponse|Response A Response instance
     */
    public function edit(
        Request $request,
        Reduction $reduction
    ): Response {
        $form = $this->createForm(ReductionType::class, $reduction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reduction->setSlug(Slugger::slugify($reduction->getTitle()));
            $this->em->flush();

            $this->addFlash('success', $this->translator->trans('reduction.edit.flash.success', [], 'flashes'));

            return $this->redirectToRoute('app_reduction_index', [
                'id' => $reduction->getId(),
            ]);
        }

        return $this->render('admin/reduction/edit.html.twig', [
            'reduction' => $reduction,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Deletes a Reduction object
     *
     * @param Request $request POST'ed data
     * @param Reduction $reduction Reduction given by an id
     *
     * @Route("/{slug}", methods={"DELETE"})
     * @return RedirectResponse A Response instance
     */
    public function delete(
        Request $request,
        Reduction $reduction
    ): RedirectResponse {
        if ($this->isCsrfTokenValid('delete'.$reduction->getId(), $request->request->get('_token'))) {
            $this->em->remove($reduction);
            $this->em->flush();

            $this->addFlash('success', $this->translator->trans('reduction.delete.flash.success', [], 'flashes'));
        }

        return $this->redirectToRoute('app_reduction_index');
    }
}
