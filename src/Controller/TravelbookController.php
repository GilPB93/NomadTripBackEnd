<?php

namespace App\Controller;

use App\Entity\Travelbook;
use App\Repository\TravelbookRepository;
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


#[Route('/api/travelbook' , name: 'app_api_travelbook_')]
class TravelbookController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private SerializerInterface $serializer,
        private TravelbookRepository $repository,
        private UrlGeneratorInterface $urlGenerator,
    ){
    }

    //CREATE - POST
    #[Route(name: 'new', methods: ['POST'])]
    #[OA\Post(
        path: '/api/travelbook',
        summary: 'Create a travelbook',
        requestBody: new OA\RequestBody(
            description: 'Travelbook data to create',
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'title', type: 'string', example: 'Titre du carnet de voyage'),
                    new OA\Property(property: 'departureAt', type: 'string', format: 'date-time', example: '2021-12-31T23:59:59'),
                    new OA\Property(property: 'comebackAt', type: 'string', format: 'date-time', example: '2022-01-01T00:00:00'),
                ],
                type: 'object'
            )
        ),
        tags: ['Travelbook'],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Travelbook created successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 1),
                        new OA\Property(property: 'title', type: 'string', example: 'Titre du travelbook'),
                        new OA\Property(property: 'departureAt', type: 'string', format: 'date-time'),
                        new OA\Property(property: 'comebackAt', type: 'string', format: 'date-time'),
                        new OA\Property(property: 'createdAt', type: 'string', format: 'date-time'),
                        new OA\Property(property: 'userId', type: 'integer', example: 1)
                    ],
                    type: 'object'
                )
            )
        ]
    )]
    public function new(Request $request): JsonResponse
    {
        $travelbook = $this->serializer->deserialize($request->getContent(), Travelbook::class, 'json');
        $travelbook->setUpdatedAt(new \DateTimeImmutable());

        $user = $this->getUser()->getId();
        $travelbook->setUser($user);

        $this->manager->persist($travelbook);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($travelbook, 'json');
        $location = $this->urlGenerator->generate('app_api_travelbook_show', ['id' => $travelbook->getId()]);

        return new JsonResponse($responseData, Response::HTTP_CREATED, ['Location' => $location], true);
    }

    //READ - GET
    #[Route('/{id}', name: 'show', methods: ['GET'])]
    #[OA\Get(
        path: '/api/travelbook/{id}',
        summary: 'Show a travelbook by ID',
        tags: ['Travelbook'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID of the travelbook to show',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Travelbook found successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 1),
                        new OA\Property(property: 'title', type: 'string', example: 'Titre du travelbook'),
                        new OA\Property(property: 'departureAt', type: 'string', format: 'date-time'),
                        new OA\Property(property: 'comebackAt', type: 'string', format: 'date-time'),
                        new OA\Property(property: 'flightNumber', type: 'string'),
                        new OA\Property(property: 'accommodation', type: 'string'),
                        new OA\Property(property: 'listPlaces', type: 'array', items: new OA\Items(type: 'string', example: 'Place name')),
                        new OA\Property(property: 'listFB', type: 'array', items: new OA\Items(type: 'string', example: 'Name of the restaurant/bar to try')),
                        new OA\Property(property: 'listSouvenirs', type: 'array', items: new OA\Items(type: 'string', example: 'For who')),
                        new OA\Property(property: 'listPhotos', type: 'array', items: new OA\Items(type: 'string', example: 'Photo URL')),
                        new OA\Property(property: 'createdAt', type: 'string', format: 'date-time'),
                        new OA\Property(property: 'updatedAt', type: 'string', format: 'date-time'),
                        new OA\Property(property: 'userId', type: 'integer', example: 1)

                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Travelbook not found'
            )
        ]
    )]
    public function show(int $id): JsonResponse
    {
        $travelbook = $this->repository->find($id);
        if ($travelbook) {
            $responseData = $this->serializer->serialize($travelbook, 'json');
            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }
        return new JsonResponse('Travelbook not found', Response::HTTP_NOT_FOUND);
    }

    //UPDATE - PUT
    #[Route('/{id}', name: 'edit', methods: ['PUT'])]
    #[OA\Put(
        path: '/api/travelbook/{id}',
        summary: 'Edit a travelbook by ID',
        requestBody: new OA\RequestBody(
            description: 'New data of the travelbook to update',
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'id', type: 'integer', example: 1),
                    new OA\Property(property: 'title', type: 'string', example: 'Titre du travelbook'),
                    new OA\Property(property: 'departureAt', type: 'string', format: 'date-time'),
                    new OA\Property(property: 'comebackAt', type: 'string', format: 'date-time'),
                    new OA\Property(property: 'flightNumber', type: 'string'),
                    new OA\Property(property: 'accommodation', type: 'string'),
                    new OA\Property(property: 'listPlaces', type: 'array', items: new OA\Items(type: 'string', example: 'Place name')),
                    new OA\Property(property: 'listFB', type: 'array', items: new OA\Items(type: 'string', example: 'Name of the restaurant/bar to try')),
                    new OA\Property(property: 'listSouvenirs', type: 'array', items: new OA\Items(type: 'string', example: 'For who')),
                    new OA\Property(property: 'listPhotos', type: 'array', items: new OA\Items(type: 'string', example: 'Photo URL')),
                    new OA\Property(property: 'createdAt', type: 'string', format: 'date-time'),
                    new OA\Property(property: 'updatedAt', type: 'string', format: 'date-time'),
                    new OA\Property(property: 'userId', type: 'integer', example: 1)

                ],
                type: 'object'
            )
        ),
        tags: ['Travelbook'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID of the travelbook to edit',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Travelbook updated successfully'
            ),
            new OA\Response(
                response: 404,
                description: 'Travelbook not found'
            )
        ]
    )]
    public function edit(Request $request, int $id): JsonResponse
    {
        $travelbook = $this->repository->find($id);
        if ($travelbook) {
            $this->serializer->deserialize(
                $request->getContent(),
                Travelbook::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $travelbook]);
            $travelbook->setUpdatedAt(new \DateTimeImmutable());
            $this->manager->flush();

            return new JsonResponse('Travelbook updated', Response::HTTP_OK);
        }
        return new JsonResponse('Travelbook not found', Response::HTTP_NOT_FOUND);
    }

    //DELETE - DELETE
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    #[OA\Delete(
        path: '/api/travelbook/{id}',
        summary: 'Delete a travelbook by ID',
        tags: ['Travelbook'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID of the travelbook to delete',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 204,
                description: 'Travelbook deleted successfully'
            ),
            new OA\Response(
                response: 404,
                description: 'Travelbook not found'
            )
        ]
    )]
    public function delete(int $id): JsonResponse
    {
        $travelbook = $this->repository->find($id);
        if ($travelbook) {
            $this->manager->remove($travelbook);
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }
        return new JsonResponse('Travelbook not found', Response::HTTP_NOT_FOUND);
    }
}