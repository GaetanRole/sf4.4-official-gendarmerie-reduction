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

/**
 * AdminOpinion Controller Class
 *
 * @todo Add patterns on each methods (mediator, adapter...)
 *
 * @category    Opinion
 * @package     App\Controller\Admin
 * @author      Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 *
 * @Route("/admin/opinion")
 * @IsGranted("ROLE_ADMIN")
 */
class AdminOpinionController extends AbstractController
{
    /**
     * Displays a form to edit an existing Opinion entity
     *
     * @todo Probably have to add a dynamic edit below a Reduction
     *
     * @param Request $request POST'ed data
     * @param Opinion $opinion Opinion given by an id
     * @param EntityManagerInterface $em Entity Manager
     *
     * @Route("/{id<\d+>}/edit", name="admin_opinion_edit", methods={"GET","POST"})
     * @return RedirectResponse|Response A Response instance
     */
    public function edit(
        Request $request,
        Opinion $opinion,
        EntityManagerInterface $em
    ): Response {
        $form = $this->createForm(OpinionType::class, $opinion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash(
                'success',
                'Le commentaire a bien été édité.'
            );

            return $this->redirectToRoute('admin_index', [
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
     * @param EntityManagerInterface $em Entity Manager
     *
     * @Route("/{id<\d+>}", name="admin_opinion_delete", methods={"DELETE"})
     * @return RedirectResponse A Response instance
     */
    public function delete(
        Request $request,
        Opinion $opinion,
        EntityManagerInterface $em
    ): RedirectResponse {
        if ($this->isCsrfTokenValid('delete'.$opinion->getId(), $request->request->get('_token'))) {
            $em->remove($opinion);
            $em->flush();

            $this->addFlash(
                'success',
                'Le commentaire a bien été supprimé.'
            );
        }

        return $this->redirectToRoute('admin_index');
    }
}
