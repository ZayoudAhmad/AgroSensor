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
        description: 'Adds a new sensor data entry to the system.',
        requestBody: new OA\RequestBody(
            description: 'Sensor data payload',
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                properties: [
                    new OA\Property(property: 'nitrogen', type: 'number', example: 12.5, description: 'Nitrogen content in the soil'),
                    new OA\Property(property: 'phosphorus', type: 'number', example: 8.3, description: 'Phosphorus content in the soil'),
                    new OA\Property(property: 'potassium', type: 'number', example: 9.1, description: 'Potassium content in the soil'),
                    new OA\Property(property: 'temperature', type: 'number', example: 22.4, description: 'Temperature in degrees Celsius'),
                    new OA\Property(property: 'ph', type: 'number', example: 6.7, description: 'pH level of the soil'),
                    new OA\Property(property: 'humidity', type: 'number', example: 75.3, description: 'Humidity percentage'),
                    new OA\Property(property: 'rainfall', type: 'number', example: 100.0, description: 'Rainfall in millimeters'),
                    new OA\Property(property: 'timestamp', type: 'string', format: 'date-time', example: '2024-11-15T10:30:00Z', description: 'Timestamp of the data collection')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Sensor data created successfully'),
            new OA\Response(response: 400, description: 'Validation error')
        ]
    )]
    public function new(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Validate input
        if (
            !$data || 
            !isset($data['nitrogen']) || 
            !isset($data['phosphorus']) || 
            !isset($data['potassium']) || 
            !isset($data['temperature']) || 
            !isset($data['ph']) || 
            !isset($data['humidity']) || 
            !isset($data['rainfall']) || 
            !isset($data['timestamp'])
        ) {
            return $this->json(['error' => 'Invalid input'], 400);
        }

        try {
            // Parse timestamp
            $timestamp = new \DateTime($data['timestamp']);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Invalid timestamp format'], 400);
        }

        // Create a new SensorData entity
        $sensorData = new SensorData();
        $sensorData->setNitrogen($data['nitrogen']);
        $sensorData->setPhosphorus($data['phosphorus']);
        $sensorData->setPotassium($data['potassium']);
        $sensorData->setTemperature($data['temperature']);
        $sensorData->setPh($data['ph']);
        $sensorData->setHumidity($data['humidity']);
        $sensorData->setRainfall($data['rainfall']);
        $sensorData->setTimestamp($timestamp);

        // Persist data
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
