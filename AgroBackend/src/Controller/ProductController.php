<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\ProductService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[Route('/api/product')]
#[OA\Tag(name: 'Product')]
class ProductController extends AbstractController
{
    private ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    #[Route('', name: 'app_product_index', methods: ['GET'])]
    #[OA\Get(
        summary: 'Get all products',
        description: 'Retrieve a list of all products in the system',
        responses: [
            new OA\Response(response: 200, description: 'List of products')
        ]
    )]
    public function index(ProductRepository $productRepository): JsonResponse
    {
        $products = $productRepository->findAll();
        $productsJson = array_map([$this->productService, 'mapToJson'], $products);

        return $this->json($productsJson, 200);
    }

    #[Route('/new', name: 'product_add', methods: ['POST'])]
    #[OA\Post(
        summary: 'Add a new Product with an image',
        description: 'Adds a new Product and uploads an image',
        requestBody: new OA\RequestBody(
            description: 'Request body for adding a new Product',
            required: true,
            content: [
                'multipart/form-data' => new OA\MediaType(
                    mediaType: 'multipart/form-data',
                    schema: new OA\Schema(
                        type: 'object',
                        properties: [
                            new OA\Property(
                                property: 'title',
                                type: 'string',
                                example: 'Product Name'
                            ),
                            new OA\Property(
                                property: 'price',
                                type: 'number',
                                example: 19.99
                            ),
                            new OA\Property(
                                property: 'image',
                                type: 'string',
                                format: 'binary',
                                description: 'Image file to upload'
                            )
                        ]
                    )
                )
            ]
        ),
        responses: [
            new OA\Response(response: 201, description: 'Product created successfully'),
            new OA\Response(response: 400, description: 'Validation error or file upload issue')
        ]
    )]
    public function addProduct(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $title = $request->request->get('title');
        $price = $request->request->get('price');
        $imageFile = $request->files->get('image');

        // Validate fields
        if (!$title || !$price || !$imageFile instanceof UploadedFile) {
            return $this->json(['error' => 'Invalid input or missing fields'], 400);
        }

        try {
            // Define the uploads directory
            $uploadsDirectory = $this->getParameter('kernel.project_dir') . '/public/uploads/products';
            if (!is_dir($uploadsDirectory)) {
                mkdir($uploadsDirectory, 0777, true);
            }

            // Generate a unique file name
            $fileName = uniqid() . '.' . $imageFile->guessExtension();
            $imageFile->move($uploadsDirectory, $fileName);

            // Create a new Product entity
            $product = new Product();
            $product->setTitle($title);
            $product->setPrice((float)$price);
            $product->setImage($fileName); // Save only the file name, not the full path

            $entityManager->persist($product);
            $entityManager->flush();

            return $this->json(
                [
                    'message' => 'Product created successfully',
                    'product' => $this->productService->mapToJson($product),
                ],
                201
            );
        } catch (\Exception $e) {
            return $this->json(['error' => 'File upload failed: ' . $e->getMessage()], 500);
        }
    }


    #[Route('/{id}', name: 'app_product_show', methods: ['GET'])]
    #[OA\Get(
        summary: 'Get a specific product',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Product ID', schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Product details'),
            new OA\Response(response: 404, description: 'Product not found')
        ]
    )]
    public function show(Product $product): JsonResponse
    {
        return $this->json($this->productService->mapToJson($product), 200);
    }

    #[Route('/{id}', name: 'app_product_delete', methods: ['DELETE'])]
    #[OA\Delete(
        summary: 'Delete a product',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Product ID', schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 204, description: 'Product deleted successfully'),
            new OA\Response(response: 404, description: 'Product not found')
        ]
    )]
    public function delete(Product $product, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($product);
        $entityManager->flush();

        return $this->json(null, 204);
    }
}
