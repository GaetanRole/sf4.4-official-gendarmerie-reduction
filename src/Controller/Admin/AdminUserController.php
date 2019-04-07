<?php

/**
 * AdminUser Controller File
 *
 * @category    User
 * @author      Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */

namespace App\Controller\Admin;

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
 * @todo Add patterns on each methods (mediator, adapter...)
 *
 * @Route("/admin/user")
 * @IsGranted("ROLE_ADMIN")
 */
class AdminUserController extends AbstractController
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
     * AdminUserController constructor.
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
     * AdminUser home page
     *
     * @param UserRepository $userRepository User manager
     *
     * @Route("/", methods={"GET"})
     * @return     Response A Response instance
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('admin/user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * Adding one User
     *
     * @link https://github.com/Innmind/TimeContinuum Global clock
     * @param Request $request POST'ed data
     * @param UserPasswordEncoderInterface $passwordEncoder Var to encode password
     * @param GlobalClock $clock Global project's clock
     *
     * @Route("/new", methods={"GET","POST"})
     * @return RedirectResponse|Response A Response instance
     * @throws \Exception Datetime Exception
     */
    public function new(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        GlobalClock $clock
    ): Response {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $user->getPlainPassword()
                )
            );
            $user->setCreationDate($clock->getNowInDateTime());
            $this->em->persist($user);
            $this->em->flush();

            $this->addFlash('success', $this->translator->trans('user.new.flash.success', [], 'flashes'));

            return $this->redirectToRoute('app_admin_adminuser_index');
        }

        return $this->render('admin/user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing User entity.
     *
     * @param Request $request POST'ed data
     * @param User $user User given by an uuid
     * @param UserPasswordEncoderInterface $encoder
     *
     * @Route("/{uuid}/edit", methods={"GET","POST"})
     * @IsGranted("edit", subject="user", message="An admin can only be edited by a super admin account.")
     * @return RedirectResponse|Response A Response instance
     */
    public function edit(
        Request $request,
        User $user,
        UserPasswordEncoderInterface $encoder
    ): Response {
        $form
            = $this->createForm(UserType::class, $user);
        $formChangePassword
            = $this->createForm(ChangePasswordType::class, $user);

        $form->handleRequest($request);
        $formChangePassword->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            $this->addFlash('success', $this->translator->trans('user.edit.account.flash.success', [], 'flashes'));

            return $this->redirectToRoute('app_admin_adminuser_index', [
                'id' => $user->getId(),
            ]);
        }

        if ($formChangePassword->isSubmitted() && $formChangePassword->isValid()) {
            $user
                ->setPassword(
                    $encoder
                        ->encodePassword(
                            $user,
                            $formChangePassword->get('plainPassword')->getData()
                        )
                );

            $this->em->flush();

            $this->addFlash('success', $this->translator->trans('user.edit.password.flash.success', [], 'flashes'));

            return $this->redirectToRoute('app_admin_adminuser_index', [
                'id' => $user->getId(),
            ]);
        }


        return $this->render('admin/user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'formChangePassword' => $formChangePassword->createView()
        ]);
    }

    /**
     * Deletes a User object.
     *
     * @param Request $request POST'ed data
     * @param User $user User given by an uuid
     *
     * @Route("/{uuid}", methods={"DELETE"})
     * @IsGranted("delete", subject="user", message="An admin can only be deleted by a super admin account.")
     * @return RedirectResponse A Response instance
     */
    public function delete(
        Request $request,
        User $user
    ): RedirectResponse {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            if ($user->getOpinions()->count() > 0 || $user->getReductions()->count() > 0) {
                $this->addFlash('danger', $this->translator->trans('user.delete.flash.danger', [], 'flashes'));
                return $this->redirectToRoute('app_admin_adminuser_index');
            }

            $this->addFlash('success', $this->translator->trans('user.delete.flash.success', [], 'flashes'));

            $this->em->remove($user);
            $this->em->flush();
        }

        return $this->redirectToRoute('app_admin_adminuser_index');
    }
}
