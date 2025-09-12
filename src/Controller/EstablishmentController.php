<?php

namespace App\Controller;

use App\Entity\Main\Establishment;
use App\Factory\EstablishmentFactory;
use App\Repository\Main\EstablishmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class EstablishmentController extends AbstractController
{
    #[Route('/establishment', name: 'app_establishment')]
    public function index(EstablishmentRepository $establishmentRepository): Response
    {
        return $this->render('establishment/index.html.twig', [
            'establishments' => $establishmentRepository->findAll(),
        ]);
    }

    #[Route('/establishment/new', name: 'app_new_establishment')]
    public function new(EntityManagerInterface $em): Response
    {
        $establishment = EstablishmentFactory::createOne(['tenantId' => 1]);

        $em->persist($establishment);
        $em->flush();

        return $this->redirectToRoute('app_establishment');
    }
}
