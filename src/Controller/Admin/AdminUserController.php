<?php

namespace App\Controller\Admin;

use Exception;
use App\Entity\User;
use App\Form\Type\ChangePasswordType;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Service\GlobalClock;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @todo    Add patterns on each methods (mediator, adapter...).
 *
 * @Route("/admin/user", name="app_admin_user_")
 * @IsGranted("ROLE_ADMIN")
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
class AdminUserController extends AbstractController
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
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('admin/user/index.html.twig', ['users' => $userRepository->findAll()]);
    }

    /**
     * @Route("/new", name="new", methods={"GET","POST"})
     * @return  RedirectResponse|Response A Response instance
     * @throws  Exception Datetime Exception
     */
    public function new(Request $request, UserPasswordEncoderInterface $passwordEncoder, GlobalClock $clock)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($passwordEncoder->encodePassword($user, $user->getPlainPassword()));
            $user->setCreationDate($clock->getNowInDateTime());

            $this->em->persist($user);
            $this->em->flush();

            $this->addFlash('success', $this->translator->trans('user.new.flash.success', [], 'flashes'));
            return $this->redirectToRoute('app_admin_user_index');
        }

        return $this->render('admin/user/new.html.twig', ['user' => $user, 'form' => $form->createView()]);
    }

    /**
     * @Route("/{uuid<^.{36}$>}/edit", name="edit", methods={"GET","POST"})
     * @IsGranted("edit", subject="user", message="An admin can only be edited by a super admin account.")
     * @return  RedirectResponse|Response A Response instance
     */
    public function edit(Request $request, User $user, UserPasswordEncoderInterface $encoder)
    {
        $form = $this->createForm(UserType::class, $user);
        $formChangePassword = $this->createForm(ChangePasswordType::class, $user);

        $form->handleRequest($request);
        $formChangePassword->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            $this->addFlash('success', $this->translator->trans('user.edit.account.flash.success', [], 'flashes'));
            return $this->redirectToRoute('app_admin_user_index');
        }

        if ($formChangePassword->isSubmitted() && $formChangePassword->isValid()) {
            $user->setPassword($encoder->encodePassword($user, $formChangePassword->get('plainPassword')->getData()));
            $this->em->flush();

            $this->addFlash('success', $this->translator->trans('user.edit.password.flash.success', [], 'flashes'));
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
    public function delete(Request $request, User $user): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            if ($user->getOpinions()->count() > 0 || $user->getReductions()->count() > 0) {
                $this->addFlash('danger', $this->translator->trans('user.delete.flash.danger', [], 'flashes'));
                return $this->redirectToRoute('app_admin_user_index');
            }

            $this->addFlash('success', $this->translator->trans('user.delete.flash.success', [], 'flashes'));
            $this->em->remove($user);
            $this->em->flush();
        }

        return $this->redirectToRoute('app_admin_user_index');
    }
}
