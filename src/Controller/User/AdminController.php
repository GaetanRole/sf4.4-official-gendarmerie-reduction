<?php

declare(strict_types = 1);

namespace App\Controller\User;

use \Exception;
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
 * @todo See voter exception translation message
 * @Route("/admin/user", name="app_admin_user_")
 * @IsGranted("ROLE_ADMIN")
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class AdminController extends AbstractController
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
        return $this->render('user/admin/index.html.twig', [
            'users' => $this->entityRepository->getRepository(User::class)->findBy([], ['username' => 'ASC'])
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET","POST"})
     * @return  RedirectResponse|Response A Response instance
     * @throws  Exception Datetime Exception
     */
    public function new(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($passwordEncoder->encodePassword($user, $user->getPlainPassword()));

            $this->entityRepository->save($user);
            return $this->redirectToRoute('app_admin_user_index');
        }

        return $this->render('user/admin/new.html.twig', ['user' => $user, 'form' => $form->createView()]);
    }

    /**
     * @Route("/{uuid<^.{36}$>}/edit", name="edit", methods={"GET","POST"})
     * @IsGranted("edit", subject="user", message="You do not have rights to do so.")
     * @return  RedirectResponse|Response A Response instance
     * @throws  Exception Datetime Exception
     */
    public function edit(Request $request, User $user, UserPasswordEncoderInterface $encoder): Response
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

        return $this->render('user/admin/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'formChangePassword' => $formChangePassword->createView()
        ]);
    }

    /**
     * @Route("/{uuid<^.{36}$>}/status", name="change_status", methods={"PUT"})
     * @IsGranted("status", subject="user", message="You do not have rights to do so.")
     */
    public function changeStatus(Request $request, User $user): RedirectResponse
    {
        if ($this->isCsrfTokenValid('status'.$user->getUuid()->toString(), $request->request->get('_token'))) {
            $user->getIsActive() ? $user->setIsActive(false) : $user->setIsActive(true);
            $this->entityRepository->update($user);
        }

        return $this->redirectToRoute('app_admin_user_index');
    }

    /**
     * @Route("/{uuid<^.{36}$>}", name="delete", methods={"DELETE"})
     * @IsGranted("delete", subject="user", message="You do not have rights to do so.")
     */
    public function delete(Request $request, User $user, TranslatorInterface $translator): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$user->getUuid()->toString(), $request->request->get('_token'))) {
            if ($user->getOpinions()->count() > 0 || $user->getReductions()->count() > 0) {
                $this->addFlash('danger', $translator->trans('user.delete.flash.danger', [], 'flashes'));
                return $this->redirectToRoute('app_admin_user_index');
            }
            $this->entityRepository->delete($user);
        }

        return $this->redirectToRoute('app_admin_user_index');
    }
}
