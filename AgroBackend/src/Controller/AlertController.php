<?php

namespace App\Controller;

use App\Entity\Alert;
use App\Repository\AlertRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[Route('/api/alert')]
#[OA\Tag(name: 'Alert')]
class AlertController extends AbstractController
{
    #[Route('/', name: 'app_alert_index', methods: ['GET'])]
    #[OA\Get(
        summary: 'Get all alerts',
        description: 'Retrieve a list of all alerts in the system',
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of alerts'
            )
        ]
    )]
    public function index(AlertRepository $alertRepository): JsonResponse
    {
        $alerts = $alertRepository->findAll();
        return $this->json($alerts, 200, [], ['groups' => 'alert:read']);
    }

    #[Route('/new', name: 'app_alert_new', methods: ['POST'])]
    #[OA\Post(
        summary: 'Create a new alert',
        requestBody: new OA\RequestBody(
            description: 'Alert data',
            required: true
        ),
        responses: [
            new OA\Response(response: 201, description: 'Alert created successfully'),
            new OA\Response(response: 400, description: 'Validation error')
        ]
    )]
    public function new(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data || empty($data['message']) || empty($data['severity'])) {
            return $this->json(['error' => 'Invalid input'], 400);
        }

        $alert = new Alert();
        $alert->setMessage($data['message']);
        $alert->setSeverity($data['severity']);
        $alert->setTimestamp(new \DateTime());

        $entityManager->persist($alert);
        $entityManager->flush();

        return $this->json(['message' => 'Alert created successfully'], 201);
    }

    #[Route('/{id}', name: 'app_alert_show', methods: ['GET'])]
    #[OA\Get(
        summary: 'Get a specific alert',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Alert ID', schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Alert details'
            ),
            new OA\Response(response: 404, description: 'Alert not found')
        ]
    )]
    public function show(Alert $alert): JsonResponse
    {
        return $this->json($alert, 200, [], ['groups' => 'alert:read']);
    }

    #[Route('/{id}', name: 'app_alert_delete', methods: ['DELETE'])]
    #[OA\Delete(
        summary: 'Delete an alert',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Alert ID', schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 204, description: 'Alert deleted successfully'),
            new OA\Response(response: 404, description: 'Alert not found')
        ]
    )]
    public function delete(Alert $alert, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($alert);
        $entityManager->flush();

        return $this->json(null, 204);
    }
}
