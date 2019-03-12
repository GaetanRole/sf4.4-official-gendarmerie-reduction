<?php

/**
 * AdminReduction Controller File
 *
 * PHP Version 7.2
 *
 * @category    Reduction
 * @package     App\Controller\Admin
 * @version     1.0
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

/**
 * AdminReduction Controller Class
 *
 * @todo Add patterns on each methods (mediator, adapter...)
 *
 * @category    Reduction
 * @package     App\Controller\Admin
 * @author      Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 *
 * @Route("/admin/reduction")
 * @IsGranted("ROLE_ADMIN")
 */
class AdminReductionController extends AbstractController
{
    /**
     * Displays a form to edit a existing Reduction entity
     *
     * @param Request $request POST'ed data
     * @param Reduction $reduction Reduction given by an id
     * @param EntityManagerInterface $em Entity Manager
     *
     * @Route("/{id<\d+>}/edit", name="reduction_edit", methods={"GET","POST"})
     * @return RedirectResponse|Response A Response instance
     */
    public function edit(
        Request $request,
        Reduction $reduction,
        EntityManagerInterface $em
    ): Response {
        $form = $this->createForm(ReductionType::class, $reduction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reduction->setSlug(Slugger::slugify($reduction->getTitle()));
            $em->flush();

            $this->addFlash(
                'success',
                'La réduction a bien été éditée.'
            );

            return $this->redirectToRoute('reduction_index', [
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
     * @param EntityManagerInterface $em Entity Manager
     *
     * @Route("/{id<\d+>}", name="reduction_delete", methods={"DELETE"})
     * @return RedirectResponse A Response instance
     */
    public function delete(
        Request $request,
        Reduction $reduction,
        EntityManagerInterface $em
    ): RedirectResponse {
        if ($this->isCsrfTokenValid('delete'.$reduction->getId(), $request->request->get('_token'))) {
            $em->remove($reduction);
            $em->flush();

            $this->addFlash(
                'success',
                'La réduction et tous les commentaires liés sont bien supprimés.'
            );
        }

        return $this->redirectToRoute('reduction_index');
    }
}
