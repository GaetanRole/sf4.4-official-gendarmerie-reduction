<?php

namespace App\Controller\Admin;

use Exception;
use App\Entity\User;
use App\Form\UserType;
use App\Form\Type\ChangePasswordType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Repository\ModelAdapter\EntityRepositoryInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/admin/user", name="app_admin_user_")
 * @IsGranted("ROLE_ADMIN")
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
class AdminUserController extends AbstractController
{
    /** @var EntityRepositoryInterface */
    private $entityRepository;

    public function __construct(EntityRepositoryInterface $entityRepository)
    {
        $this->entityRepository = $entityRepository;
    }

    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('admin/user/index.html.twig', [
            'users' => $this->entityRepository->getRepository(User::class)->findBy([], ['username' => 'ASC'])
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET","POST"})
     * @return  RedirectResponse|Response A Response instance
     * @throws  Exception Datetime Exception
     */
    public function new(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($passwordEncoder->encodePassword($user, $user->getPlainPassword()));

            $this->entityRepository->save($user);
            return $this->redirectToRoute('app_admin_user_index');
        }

        return $this->render('admin/user/new.html.twig', ['user' => $user, 'form' => $form->createView()]);
    }

    /**
     * @Route("/{uuid<^.{36}$>}/edit", name="edit", methods={"GET","POST"})
     * @IsGranted("edit", subject="user", message="An admin can only be edited by a super admin account.")
     * @return  RedirectResponse|Response A Response instance
     * @throws  Exception Datetime Exception
     */
    public function edit(Request $request, User $user, UserPasswordEncoderInterface $encoder)
    {
        $form = $this->createForm(UserType::class, $user);
        $formChangePassword = $this->createForm(ChangePasswordType::class, $user);

        $form->handleRequest($request);
        $formChangePassword->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityRepository->update($user);
            return $this->redirectToRoute('app_admin_user_index');
        }

        if ($formChangePassword->isSubmitted() && $formChangePassword->isValid()) {
            $user->setPassword($encoder->encodePassword($user, $formChangePassword->get('plainPassword')->getData()));

            $this->entityRepository->update($user);
            return $this->redirectToRoute('app_admin_user_index');
        }

        return $this->render('admin/user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'formChangePassword' => $formChangePassword->createView()
        ]);
    }

    /**
     * @Route("/{uuid<^.{36}$>}", name="delete", methods={"DELETE"})
     * @IsGranted("delete", subject="user", message="An admin can only be deleted by a super admin account.")
     */
    public function delete(Request $request, User $user, TranslatorInterface $translator): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            if ($user->getOpinions()->count() > 0 || $user->getReductions()->count() > 0) {
                $this->addFlash('danger', $translator->trans('user.delete.flash.danger', [], 'flashes'));
                return $this->redirectToRoute('app_admin_user_index');
            }
            $this->entityRepository->delete($user);
        }

        return $this->redirectToRoute('app_admin_user_index');
    }
}
