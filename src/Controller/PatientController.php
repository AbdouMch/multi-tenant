<?php

namespace App\Controller;

use App\Entity\Main\Establishment;
use App\Repository\Tenant\PatientRepository;
use Hakam\MultiTenancyBundle\Event\SwitchDbEvent;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\Requirement\Requirement;
#[Route('/patient')]
final class PatientController extends AbstractController
{
    #[Route('/{establishment_id}', name: 'tenant_all_patients')]
    public function index(
        #[MapEntity(mapping: ['establishment_id' => 'publicId'])]
        Establishment $establishment,
        PatientRepository $patientRepository,
        EventDispatcherInterface $eventDispatcher
    ): Response
    {
        $eventDispatcher->dispatch(new SwitchDbEvent($establishment->getTenantId()));

        return $this->render('patient/index.html.twig', [
            'patients' => $patientRepository->findAll(),
        ]);
    }
}
