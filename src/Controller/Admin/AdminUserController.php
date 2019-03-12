<?php

/**
 * AdminUser Controller File
 *
 * PHP Version 7.2
 *
 * @category    User
 * @package     App\Controller\Admin
 * @version     1.0
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

/**
 * AdminUser Controller Class
 *
 * @todo Add patterns on each methods (mediator, adapter...)
 *
 * @category    User
 * @package     App\Controller\Admin
 * @author      Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 *
 * @Route("/admin/user")
 * @IsGranted("ROLE_ADMIN")
 */
class AdminUserController extends AbstractController
{
    /**
     * AdminUser home page
     *
     * @param UserRepository $userRepository User manager
     *
     * @Route("/", methods={"GET"}, name="admin_user_index")
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
     * @param EntityManagerInterface $em Entity Manager
     * @param UserPasswordEncoderInterface $passwordEncoder Var to encode password
     * @param GlobalClock $clock Global project's clock
     *
     * @Route("/new", name="admin_user_new", methods={"GET","POST"})
     * @return RedirectResponse|Response A Response instance
     * @throws \Exception Datetime Exception
     */
    public function new(
        Request $request,
        EntityManagerInterface $em,
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
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'L\'utilisateur a bien été ajouté.');

            return $this->redirectToRoute('admin_user_index');
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
     * @param User $user User given by an id
     * @param EntityManagerInterface $em Entity Manager
     * @param UserPasswordEncoderInterface $encoder
     *
     * @Route("/{id<\d+>}/edit", name="admin_user_edit", methods={"GET","POST"})
     * @IsGranted("edit", subject="user", message="An admin can only be edited by a super admin account.")
     * @return RedirectResponse|Response A Response instance
     */
    public function edit(
        Request $request,
        User $user,
        EntityManagerInterface $em,
        UserPasswordEncoderInterface $encoder
    ): Response {
        $form
            = $this->createForm(UserType::class, $user);
        $formChangePassword
            = $this->createForm(ChangePasswordType::class, $user);

        $form->handleRequest($request);
        $formChangePassword->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash(
                'success',
                'L\'utilisateur a bien été édité.'
            );

            return $this->redirectToRoute('admin_user_index', [
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

            $em->flush();

            $this->addFlash(
                'success',
                'Le mot de passe a bien été édité.'
            );

            return $this->redirectToRoute('admin_user_index', [
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
     * @param User $user User given by an id
     * @param EntityManagerInterface $em Entity Manager
     *
     * @Route("/{id<\d+>}", name="admin_user_delete", methods={"DELETE"})
     * @IsGranted("delete", subject="user", message="An admin can only be deleted by a super admin account.")
     * @return RedirectResponse A Response instance
     */
    public function delete(
        Request $request,
        User $user,
        EntityManagerInterface $em
    ): RedirectResponse {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            if ($user->getOpinions()->count() > 0 || $user->getReductions()->count() > 0) {
                $this->addFlash(
                    'danger',
                    'Un utilisateur ne peut pas être supprimé si il est associé à des commentaires et articles.'
                );

                return $this->redirectToRoute('admin_user_index');
            }

            $this->addFlash(
                'success',
                'L\'utilisateur a bien été supprimé.'
            );

            $em->remove($user);
            $em->flush();
        }

        return $this->redirectToRoute('admin_user_index');
    }
}
