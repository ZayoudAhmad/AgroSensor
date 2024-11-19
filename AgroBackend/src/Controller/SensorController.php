<?php

namespace App\Controller;

use App\Entity\Sensor;
use App\Repository\SensorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[Route('/api/sensor')]
#[OA\Tag(name: 'Sensor')]
class SensorController extends AbstractController
{
    #[Route('', name: 'app_sensor_index', methods: ['GET'])]
    #[OA\Get(
        summary: 'Get all sensors',
        description: 'Retrieve a list of all sensors in the system',
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of sensors'
            )
        ]
    )]
    public function index(SensorRepository $sensorRepository): JsonResponse
    {
        $sensors = $sensorRepository->findAll();
        return $this->json($sensors, 200, [], ['groups' => 'sensor:read']);
    }

    #[Route('/new', name: 'app_sensor_new', methods: ['POST'])]
    #[OA\Post(
        summary: 'Create a new sensor',
        requestBody: new OA\RequestBody(
            description: 'Sensor data',
            required: true
        ),
        responses: [
            new OA\Response(response: 201, description: 'Sensor created successfully'),
            new OA\Response(response: 400, description: 'Validation error')
        ]
    )]
    public function new(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data || empty($data['type']) || !isset($data['latitude']) || !isset($data['longitude'])) {
            return $this->json(['error' => 'Invalid input'], 400);
        }

        $sensor = new Sensor();
        $sensor->setType($data['type']);
        $sensor->setLatitude($data['latitude']);
        $sensor->setLongitude($data['longitude']);

        $entityManager->persist($sensor);
        $entityManager->flush();

        return $this->json(['message' => 'Sensor created successfully'], 201);
    }

    #[Route('/{id}', name: 'app_sensor_show', methods: ['GET'])]
    #[OA\Get(
        summary: 'Get a specific sensor',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Sensor ID', schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Sensor details'
            ),
            new OA\Response(response: 404, description: 'Sensor not found')
        ]
    )]
    public function show(Sensor $sensor): JsonResponse
    {
        return $this->json($sensor, 200, [], ['groups' => 'sensor:read']);
    }

    #[Route('/{id}', name: 'app_sensor_delete', methods: ['DELETE'])]
    #[OA\Delete(
        summary: 'Delete a sensor',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Sensor ID', schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 204, description: 'Sensor deleted successfully'),
            new OA\Response(response: 404, description: 'Sensor not found')
        ]
    )]
    public function delete(Sensor $sensor, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($sensor);
        $entityManager->flush();

        return $this->json(null, 204);
    }
}
