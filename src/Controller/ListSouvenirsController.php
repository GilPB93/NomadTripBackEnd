<?php

namespace App\Controller;

use App\Entity\ListSouvenirs;
use App\Repository\ListSouvenirsRepository;
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



#[Route('/api/list-souvenirs', name: 'api_list_souvenirs_')]
class ListSouvenirsController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private ListSouvenirsRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    ){
    }
    //CREATE - POST
    #[Route(name: 'new', methods: ['POST'])]
    #[OA\Post(
        path: '/api/list-souvenirs',
        summary: 'Create a new list of souvenirs',
        requestBody: new OA\RequestBody(
            description: 'Data of the list of souvenirs to create',
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'forWho', type: 'string', example: 'Maman'),
                    new OA\Property(property: 'what', type: 'string', example: 'Un foulard')
                ],
                type: 'object'
            )
        ),
        tags: ['List of souvenirs'],
        responses: [
            new OA\Response(
                response: 201,
                description: 'List of souvenirs created successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 1),
                        new OA\Property(property: 'forWho', type: 'string', example: 'Maman'),
                        new OA\Property(property: 'what', type: 'string', example: 'Un foulard')
                    ],
                    type: 'object'
                )
            )
        ]
    )]
    public function new(Request $request) : JsonResponse
    {
        $listSouvenirs = $this->serializer->deserialize($request->getContent(), ListSouvenirs::class, 'json');

        $this->manager->persist($listSouvenirs);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($listSouvenirs, 'json');
        $location = $this->urlGenerator->generate('api_list_souvenirs_show',
            ['id' => $listSouvenirs->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
    }


    //READ - GET
    #[Route('/{id}', name: 'show', methods: ['GET'])]
    #[OA\Get(
        path: '/api/list-souvenirs/{id}',
        summary: 'Show a list of souvenirs by ID',
        tags: ['List of souvenirs'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID of the list of souvenirs to show',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of souvenirs found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 1),
                        new OA\Property(property: 'forWho', type: 'string', example: 'Maman'),
                        new OA\Property(property: 'what', type: 'string', example: 'Un foulard')
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: 404,
                description: 'List of souvenirs not found'
            )
        ]
    )]
    public function show(int $id) : JsonResponse
    {
        $listSouvenirs = $this->repository->findOneBy(['id' => $id]);

        if ($listSouvenirs){
            $responseData = $this->serializer->serialize($listSouvenirs, 'json');
            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    //UPDATE - PUT
    #[Route('/{id}', name: 'edit', methods: ['PUT'])]
    #[OA\Put(
        path: '/api/list-souvenirs/{id}',
        summary: 'Edit a list of souvenirs by ID',
        requestBody: new OA\RequestBody(
            description: 'New data of the list of souvenirs to update',
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'forWho', type: 'string', example: 'Maman'),
                    new OA\Property(property: 'what', type: 'string', example: 'Un foulard')
                ],
                type: 'object'
            )
        ),
        tags: ['List of souvenirs'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID of the list of souvenirs to edit',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 204,
                description: 'List of souvenirs edited successfully'
            ),
            new OA\Response(
                response: 404,
                description: 'List of souvenirs not found'
            )
        ]
    )]
    public function edit(int $id, Request $request) : JsonResponse
    {
        $listSouvenirs = $this->repository->findOneBy(['id' => $id]);
        if ($listSouvenirs) {
            $this->serializer->deserialize(
                $request->getContent(),
                ListSouvenirs::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $listSouvenirs]
            );

            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);

    }

    //DELETE - DELETE
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    #[OA\Delete(
        path: '/api/list-souvenirs/{id}',
        summary: 'Delete a list of souvenirs by ID',
        tags: ['List of souvenirs'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID of the list of souvenirs to delete',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 204,
                description: 'List of souvenirs deleted successfully'
            ),
            new OA\Response(
                response: 404,
                description: 'List of souvenirs not found'
            )
        ]
    )]
    public function delete(int $id) : JsonResponse
    {
        $listSouvenirs = $this->repository->findOneBy(['id' => $id]);
        if ($listSouvenirs){
            $this->manager->remove($listSouvenirs);
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

}
