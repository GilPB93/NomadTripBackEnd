<?php

namespace App\Controller;

use App\Entity\ListFB;
use App\Repository\ListFBRepository;
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

#[Route('/api/list-fb', name: 'app_api_list_fb_')]
class ListFBController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private ListFBRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    ){
    }

    //CREATE - POST
    #[Route (name: 'new', methods: 'POST')]
    #[OA\Post(
        path: "/api/list-fb",
        summary: "Create a new listFB",
        requestBody: new OA\RequestBody(
            description: "Data of the listFB to create",
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Name of the restaurant/bar to try"),
                    new OA\Property(property: "address", type: "text", example: "Address of the restaurant/bar to try"),
                    new OA\Property(property: "visitDateTime", type: "string", example: "date-time")
                ],
                type: "object"
            )
        ),
        tags: ["List FB"],
        responses: [
            new OA\Response(
                response: 201,
                description: "ListFB created successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "name", type: "string", example: "Name of the restaurant/bar to try"),
                        new OA\Property(property: "address", type: "text", example: "Address of the restaurant/bar to try"),
                        new OA\Property(property: "visitDateTime", type: "string", example: "date-time")
                    ],
                    type: "object"
                )
            )
        ]
    )]
    public function new(Request $request): JsonResponse
    {
        $listFB = $this->serializer->deserialize($request->getContent(), ListFB::class, 'json');

        $this->manager->persist($listFB);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($listFB, 'json');
        $location = $this->urlGenerator->generate('app_api_list_fb_show',
            ['id' => $listFB->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );

        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
    }


    //READ - GET
    #[Route('/{id}', name: 'show', methods: ['GET'])]
    #[OA\Get(
        path: "/api/list-fb/{id}",
        summary: "Show a listFB by ID",
        tags: ["List FB"],
        parameters: [
            new OA\Parameter(
                name: "id",
                description: "ID of the listFB to show",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "ListFB found successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "name", type: "string", example: "Name of the restaurant/bar to try"),
                        new OA\Property(property: "address", type: "text", example: "Address of the restaurant/bar to try"),
                        new OA\Property(property: "visitDateTime", type: "string", example: "date-time")
                    ],
                    type: "object"
                )
            ),
            new OA\Response(
                response: 404,
                description: "ListFB not found"
            )
        ]
    )]
    public function show(int $id): JsonResponse
    {
        $listFB = $this->repository->findOneBy(['id' => $id]);

        if ($listFB){
            return new JsonResponse(
                $this->serializer->serialize($listFB, 'json', [AbstractNormalizer::IGNORED_ATTRIBUTES => ['user']]),
                Response::HTTP_OK,
                [],
                true
            );
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    //UPDATE - PUT
    #[Route('/{id}', name: 'edit', methods: ['PUT'])]
    #[OA\Put(
        path: "/api/list-fb/{id}",
        summary: "Edit a listFB by ID",
        requestBody: new OA\RequestBody(
            description: "New data of the listFB to update",
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "name", type: "string", example: "New name of the restaurant/bar to try"),
                    new OA\Property(property: "address", type: "text", example: "New address of the restaurant/bar to try"),
                    new OA\Property(property: "visitDateTime", type: "string", example: "date-time")
                ],
                type: "object"
            )
        ),
        tags: ["List FB"],
        parameters: [
            new OA\Parameter(
                name: "id",
                description: "ID of the listFB to edit",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 204, description: "ListFB edited successfully"),
            new OA\Response(response: 404, description: "ListFB not found")
        ]
    )]
    public function edit(int $id, Request $request): JsonResponse
    {
        $listFB = $this->repository->findOneBy(['id' => $id]);

        if ($listFB){
            $this->serializer->deserialize(
                $request->getContent(),
                ListFB::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $listFB]
            );

            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }


    //DELETE - DELETE
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    #[OA\Delete(
        path: "/api/list-fb/{id}",
        summary: "Delete a listFB by ID",
        tags: ["List FB"],
        parameters: [
            new OA\Parameter(
                name: "id",
                description: "ID of the listFB to delete",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 204,
                description: "ListFB deleted successfully"),
            new OA\Response(
                response: 404,
                description: "ListFB not found")
        ]
    )]
    public function delete(int $id): JsonResponse
    {
        $listFB = $this->repository->findOneBy(['id' => $id]);

        if ($listFB){
            $this->manager->remove($listFB);
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
}
