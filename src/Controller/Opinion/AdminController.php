<?php

declare(strict_types=1);

namespace App\Controller\Opinion;

use \Exception;
use App\Entity\Opinion;
use App\Form\OpinionType;
use App\Repository\Adapter\RepositoryAdapterInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/opinion", name="app_admin_opinion_")
 * @IsGranted("ROLE_ADMIN")
 *
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class AdminController extends AbstractController
{
    /** @var RepositoryAdapterInterface */
    private $repositoryAdapter;

    public function __construct(RepositoryAdapterInterface $repositoryAdapter)
    {
        $this->repositoryAdapter = $repositoryAdapter;
    }

    /**
     * @Route("/{uuid<^.{36}$>}", name="delete", methods="DELETE")
     */
    public function delete(Request $request, Opinion $opinion): RedirectResponse
    {
        $slug = $opinion->getReduction()->getSlug();

        if ($this->isCsrfTokenValid('delete'.$opinion->getUuid()->toString(), $request->request->get('_token'))) {
            $this->repositoryAdapter->delete($opinion);
        }

        return $this->redirectToRoute('app_reduction_view', ['slug' => $slug]);
    }
}
