<?php

namespace App\Controller;

use App\Entity\ListPhotos;
use App\Repository\ListPhotosRepository;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/list-photos', name: 'app_api_list_photos')]
class ListPhotosController extends AbstractController
{
    public function __construct(
        private ListPhotosRepository $repository,
        private EntityManagerInterface $manager,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    ){
    }

    //CREATE - POST
    #[Route(name: 'new', methods: ['POST'])]
    #[OA\Post(
        path: '/api/list-photos',
        summary: 'Create a new photo',
        requestBody: new OA\RequestBody(
            description: 'Data of the photo to create',
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'url', type: 'string', example: 'https://www.example.com/photo.jpg')
                ],
                type: 'object'
            )
        ),
        tags: ['List of Photos'],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Photo created successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 1),
                        new OA\Property(property: 'url', type: 'string', example: 'https://www.example.com/photo.jpg'),
                        new OA\Property(property: 'addedAt', type: 'string', example: 'Date-time added')
                    ],
                    type: 'object'
                )
            )
        ]
    )]
    public function new(Request $request): JsonResponse
    {
        $listPhotos = $this->serializer->deserialize($request->getContent(), ListPhotos::class, 'json');
        $this->manager->persist($listPhotos);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($listPhotos, 'json');
        $location = $this->urlGenerator->generate(
            'app_api_list_photos_show',
            ['id' => $listPhotos->getId()],
            UrlGeneratorInterface:: ABSOLUTE_URL
        );

        return new JsonResponse($responseData, Response::HTTP_CREATED, ['Location' => $location], true);

    }

    //READ - GET
    #[Route('/{id}' , name: 'show', methods: ['GET'])]
    #[OA\Get(
        path: '/api/list-photos/{id}',
        summary: 'Get a photo by ID',
        tags: ['List of Photos'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID of the photo',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Photo found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 1),
                        new OA\Property(property: 'url', type: 'string', example: 'https://www.example.com/photo.jpg'),
                        new OA\Property(property: 'addedAt', type: 'string', example: 'Date-time added')
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Photo not found'
            )
        ]
    )]
    public function show(int $id): JsonResponse
    {
        $listPhotos = $this->repository->findOneBy(['id' => $id]);

        if ($listPhotos) {
            $responseData = $this->serializer->serialize($listPhotos, 'json');
            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    //UPDATE - PUT
    #[Route('/{id}', name: 'edit', methods: ['PUT'])]
    #[OA\Put(
        path: '/api/list-photos/{id}',
        summary: 'Update a photo by ID',
        requestBody: new OA\RequestBody(
            description: 'New data of the photo to update',
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'id', type: 'integer', example: 1),
                    new OA\Property(property: 'url', type: 'string', example: 'https://www.example.com/photo.jpg')
                ],
                type: 'object'
            )
        ),
        tags: ['List of Photos'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID of the photo to update',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Photo updated successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 1),
                        new OA\Property(property: 'url', type: 'string', example: 'https://www.example.com/photo.jpg'),
                        new OA\Property(property: 'addedAt', type: 'string', example: 'Date-time added')
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Photo not found'
            )
        ]
    )]
    public function edit(int $id, Request $request): JsonResponse
    {
        $listPhotos = $this->repository->findOneBy(['id' => $id]);

        if ($listPhotos) {
            $listPhotos = $this->serializer->deserialize($request->getContent(), ListPhotos::class, 'json');
            $this->manager->flush();

            $responseData = $this->serializer->serialize($listPhotos, 'json');
            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }


    //DELETE - DELETE
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    #[OA\Delete(
        path: '/api/list-photos/{id}',
        summary: 'Delete a photo by ID',
        tags: ['List of Photos'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID of the photo to delete',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 204,
                description: 'Photo deleted successfully'
            ),
            new OA\Response(
                response: 404,
                description: 'Photo not found'
            )
        ]
    )]
    public function delete(int $id): JsonResponse
    {
        $listPhotos = $this->repository->findOneBy(['id' => $id]);

        if ($listPhotos) {
            $this->manager->remove($listPhotos);
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

}
