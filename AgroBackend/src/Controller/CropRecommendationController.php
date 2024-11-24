<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use OpenApi\Attributes as OA;

#[Route('/api/recommendation')]
#[OA\Tag(name: 'Crop Recommendation')]
class CropRecommendationController extends AbstractController
{
    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    #[Route('', methods: ['POST'])]
    #[OA\Post(
        summary: 'Get crop recommendations based on soil and environmental data',
        description: 'Fetches crop recommendations from a machine learning model based on input data.',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                properties: [
                    new OA\Property(property: 'nitrogen', type: 'number', example: 10.5, description: 'Nitrogen content in the soil'),
                    new OA\Property(property: 'phosphorous', type: 'number', example: 8.2, description: 'Phosphorous content in the soil'),
                    new OA\Property(property: 'potassium', type: 'number', example: 12.3, description: 'Potassium content in the soil'),
                    new OA\Property(property: 'temperature', type: 'number', example: 25.7, description: 'Temperature in degrees Celsius'),
                    new OA\Property(property: 'humidity', type: 'number', example: 70.3, description: 'Humidity percentage'),
                    new OA\Property(property: 'ph', type: 'number', example: 6.5, description: 'pH level of the soil'),
                    new OA\Property(property: 'rainfall', type: 'number', example: 120.0, description: 'Rainfall in millimeters')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Top crop recommendations based on input data',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(
                            property: 'top_crops',
                            type: 'array',
                            items: new OA\Items(
                                type: 'object',
                                properties: [
                                    new OA\Property(property: 'crop', type: 'string', example: 'Wheat', description: 'Crop name'),
                                    new OA\Property(property: 'confidence', type: 'number', example: 0.85, description: 'Confidence score for the recommendation')
                                ]
                            )
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Invalid input or missing fields',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Missing fields: nitrogen, phosphorous')
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Error connecting to the Flask API',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Failed to connect to Flask API'),
                        new OA\Property(property: 'details', type: 'string', example: 'Connection timeout')
                    ]
                )
            )
        ]
    )]
    public function recommend(Request $request): JsonResponse
    {
        // Validate input
        $data = json_decode($request->getContent(), true);
        $requiredFields = ['nitrogen', 'phosphorous', 'potassium', 'temperature', 'humidity', 'ph', 'rainfall'];
        $missingFields = array_diff($requiredFields, array_keys($data));

        if (!empty($missingFields)) {
            return $this->json(['error' => 'Missing fields: ' . implode(', ', $missingFields)], 400);
        }

        // Make a POST request to the Flask API
        try {
            $flaskApiUrl = 'http://127.0.0.1:5000/predict'; // Update with your Flask API endpoint
            $response = $this->httpClient->request('POST', $flaskApiUrl, [
                'json' => $data,
            ]);

            $statusCode = $response->getStatusCode();
            if ($statusCode !== 200) {
                return $this->json(['error' => 'Flask API returned an error', 'statusCode' => $statusCode], 500);
            }

            $flaskResponse = $response->toArray();

            // Return the response from the Flask API to the client
            return $this->json($flaskResponse);

        } catch (\Exception $e) {
            return $this->json(['error' => 'Failed to connect to Flask API', 'details' => $e->getMessage()], 500);
        }
    }
}
