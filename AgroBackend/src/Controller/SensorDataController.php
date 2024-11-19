<?php

namespace App\Controller;

use App\Entity\SensorData;
use App\Repository\SensorDataRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[Route('/api/sensordata')]
#[OA\Tag(name: 'SensorData')]
class SensorDataController extends AbstractController
{
    #[Route('', name: 'app_sensor_data_index', methods: ['GET'])]
    #[OA\Get(
        summary: 'Get all sensor data',
        description: 'Retrieve a list of all sensor data entries in the system',
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of sensor data'
            )
        ]
    )]
    public function index(SensorDataRepository $sensorDataRepository): JsonResponse
    {
        $sensorData = $sensorDataRepository->findAll();
        return $this->json($sensorData, 200, [], ['groups' => 'sensorData:read']);
    }

    #[Route('/new', name: 'app_sensor_data_new', methods: ['POST'])]
    #[OA\Post(
        summary: 'Create new sensor data',
        requestBody: new OA\RequestBody(
            description: 'Sensor data payload',
            required: true
        ),
        responses: [
            new OA\Response(response: 201, description: 'Sensor data created successfully'),
            new OA\Response(response: 400, description: 'Validation error')
        ]
    )]
    public function new(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data || empty($data['sensor_id']) || !isset($data['nitrogen']) || !isset($data['phosphorus']) || !isset($data['potassium']) || !isset($data['temperature']) || !isset($data['ph'])) {
            return $this->json(['error' => 'Invalid input'], 400);
        }

        $sensorData = new SensorData();
        $sensorData->setNitrogen($data['nitrogen']);
        $sensorData->setPhosphorus($data['phosphorus']);
        $sensorData->setPotassium($data['potassium']);
        $sensorData->setTemperature($data['temperature']);
        $sensorData->setPh($data['ph']);
        $sensorData->setTimestamp(new \DateTime());

        $entityManager->persist($sensorData);
        $entityManager->flush();

        return $this->json(['message' => 'Sensor data created successfully'], 201);
    }

    #[Route('/{id}', name: 'app_sensor_data_show', methods: ['GET'])]
    #[OA\Get(
        summary: 'Get a specific sensor data entry',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Sensor data ID', schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Sensor data details'
            ),
            new OA\Response(response: 404, description: 'Sensor data not found')
        ]
    )]
    public function show(SensorData $sensorData): JsonResponse
    {
        return $this->json($sensorData, 200, [], ['groups' => 'sensorData:read']);
    }

    #[Route('/{id}', name: 'app_sensor_data_delete', methods: ['DELETE'])]
    #[OA\Delete(
        summary: 'Delete a sensor data entry',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Sensor data ID', schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 204, description: 'Sensor data deleted successfully'),
            new OA\Response(response: 404, description: 'Sensor data not found')
        ]
    )]
    public function delete(SensorData $sensorData, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($sensorData);
        $entityManager->flush();

        return $this->json(null, 204);
    }
}
