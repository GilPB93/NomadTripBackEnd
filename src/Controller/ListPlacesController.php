<?php

namespace App\Controller;

use App\Entity\ListPlaces;
use App\Repository\ListPlacesRepository;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/list-places', name: 'app_api_list_places_')]
class ListPlacesController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private ListPlacesRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    ){
    }

    //CREATE - POST
    #[Route(name: 'new', methods: ['POST'])]
    #[OA\Post(
        path: '/api/list-places',
        summary: 'Create a place to visit',
        requestBody: new OA\RequestBody(
            description: 'Data of the place to create',
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Place name'),
                    new OA\Property(property: 'address', type: 'text', example: 'Address of the place to visit'),
                    new OA\Property(property: 'visitDateTime', type: 'string', example: 'date-time')
                ],
                type: 'object'
            )
        ),
        tags: ['List of Places'],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Place created successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 1),
                        new OA\Property(property: 'name', type: 'string', example: 'Place name'),
                        new OA\Property(property: 'address', type: 'text', example: 'Address of the place to visit'),
                        new OA\Property(property: 'visitDateTime', type: 'string', example: 'date-time')
                    ],
                    type: 'object'
                )
            )
        ]
    )]
    public function new(Request $request) : JsonResponse
    {
        $listPlaces = $this->serializer->deserialize($request->getContent(), ListPlaces::class, 'json');

        $this->manager->persist($listPlaces);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($listPlaces, 'json');
        $location = $this->urlGenerator->generate('app_api_list_places_show',
            ['id' => $listPlaces->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );

        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    //READ - GET
    #[Route('/{id}', name: 'show', methods: ['GET'])]
    #[OA\Get(
        path: '/api/list-places/{id}',
        summary: 'Show a place by ID',
        tags: ['List of Places'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID of the place to show',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Place found successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 1),
                        new OA\Property(property: 'name', type: 'string', example: 'Place name'),
                        new OA\Property(property: 'address', type: 'text', example: 'Address of the place to visit'),
                        new OA\Property(property: 'visitDateTime', type: 'string', example: 'date-time')
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Place not found'
            )
        ]
    )]
    public function show(int $id) : JsonResponse
    {
        $listPlaces = $this->repository->findOneBy(['id' => $id]);

        if ($listPlaces){
            $responseData = $this->serializer->serialize($listPlaces, 'json');
            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    //UPDATE - PUT
    #[Route('/{id}', name: 'edit', methods: ['PUT'])]
    #[OA\Put(
        path: '/api/list-places/{id}',
        summary: 'Edit a place by ID',
        requestBody: new OA\RequestBody(
            description: 'New data of the place to update',
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'New place name'),
                    new OA\Property(property: 'address', type: 'text', example: 'New address of the place to visit'),
                    new OA\Property(property: 'visitDateTime', type: 'string', example: 'date-time')
                ],
                type: 'object'
            )
        ),
        tags: ['List of Places'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID of the place to edit',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Place updated successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 1),
                        new OA\Property(property: 'name', type: 'string', example: 'New place name'),
                        new OA\Property(property: 'address', type: 'text', example: 'New address of the place to visit'),
                        new OA\Property(property: 'visitDateTime', type: 'string', example: 'date-time')
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Place not found'
            )
        ]
    )]
    public function edit(int $id, Request $request) : JsonResponse
    {
        $listPlaces = $this->repository->findOneBy(['id' => $id]);
        if ($listPlaces) {
            $this->serializer->deserialize(
                $request->getContent(),
                ListPlaces::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $listPlaces]
            );

            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }


    //DELETE - DELETE
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    #[OA\Delete(
        path: '/api/list-places/{id}',
        summary: 'Delete a place by ID',
        tags: ['List of Places'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID of the place to delete',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 204,
                description: 'Place deleted successfully'
            ),
            new OA\Response(
                response: 404,
                description: 'Place not found'
            )
        ]
    )]
    public function delete(int $id) : JsonResponse
    {
        $listPlaces = $this->repository->findOneBy(['id' => $id]);
        if ($listPlaces){
            $this->manager->remove($listPlaces);
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

}
