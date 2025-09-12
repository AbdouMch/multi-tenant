<?php

namespace App\Controller;

use App\Repository\Main\TenantDbConfigRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TenantDbConfigController extends AbstractController
{
    #[Route('/tenant/db/config', name: 'app_tenant_db_config')]
    public function index(TenantDbConfigRepository $configRepository): Response
    {
        dump($configRepository->findAll());

        return $this->render('tenant_db_config/index.html.twig', [
            'controller_name' => 'TenantDbConfigController',
        ]);
    }
}
