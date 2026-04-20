<?php

namespace App\Controller;

use App\Repository\Main\TenantDbConfigRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
final class TenantDbConfigController extends AbstractController
{
    #[Route('/tenant/db/config', name: 'app_tenant_db_config')]
    public function index(TenantDbConfigRepository $configRepository): Response
    {
        return $this->render('tenant_db_config/index.html.twig', [
            'configs' => $configRepository->findAll(),
        ]);
    }
}
