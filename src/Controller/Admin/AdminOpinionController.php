<?php

/**
 * AdminOpinion Controller File
 *
 * PHP Version 7.2
 *
 * @category    Opinion
 * @package     App\Controller\Admin
 * @version     1.0
 * @author      Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */

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
 * AdminOpinion Controller Class
 *
 * @todo Add patterns on each methods (mediator, adapter...)
 *
 * @category    Opinion
 * @package     App\Controller\Admin
 * @author      Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 *
 * @Route("/{_locale}/admin/opinion", defaults={"_locale"="%locale%"})
 * @IsGranted("ROLE_ADMIN")
 */
class AdminOpinionController extends AbstractController
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
     * AdminOpinionController constructor.
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
     * Displays a form to edit an existing Opinion entity
     *
     * @todo Probably have to add a dynamic edit below a Reduction
     *
     * @param Request $request POST'ed data
     * @param Opinion $opinion Opinion given by an id
     *
     * @Route("/{id<\d+>}/edit", methods={"GET","POST"})
     * @return RedirectResponse|Response A Response instance
     */
    public function edit(
        Request $request,
        Opinion $opinion
    ): Response {
        $form = $this->createForm(OpinionType::class, $opinion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            $this->addFlash('success', $this->translator->trans('opinion.edit.flash.success', [], 'flashes'));

            return $this->redirectToRoute('app_admin_index', [
                'id' => $opinion->getId(),
            ]);
        }

        return $this->render('admin/opinion/edit.html.twig', [
            'opinion' => $opinion,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Deletes an Opinion object
     *
     * @param Request $request POST'ed data
     * @param Opinion $opinion Opinion given by an id
     *
     * @Route("/{id<\d+>}", methods={"DELETE"})
     * @return RedirectResponse A Response instance
     */
    public function delete(
        Request $request,
        Opinion $opinion
    ): RedirectResponse {
        if ($this->isCsrfTokenValid('delete'.$opinion->getId(), $request->request->get('_token'))) {
            $this->em->remove($opinion);
            $this->em->flush();

            $this->addFlash('success', $this->translator->trans('opinion.delete.flash.success', [], 'flashes'));
        }

        return $this->redirectToRoute('app_admin_index');
    }
}
