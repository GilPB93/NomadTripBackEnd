<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


#[Route('/api/user', name: 'app_api_user_')]

class UserController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private UserRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    ){
    }

    //CREATE - POST
    #[Route (name: 'new', methods: 'POST')]
    #[OA\Post(
        path: "/api/user",
        summary: "Create a new user",
        requestBody: new OA\RequestBody(
            description: "Data of the user to create",
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "firstName", type: "string", example: "Thomas"),
                    new OA\Property(property: "lastName", type: "string", example: "Dupont"),
                    new OA\Property(property: "pseudo", type: "string", example: "Dudupont"),
                    new OA\Property(property: "email", type: "string", example: "exemple@email.com"),
                    new OA\Property(property: "password", type: "string", example: "mdp"),
                ],
                type: "object"
            )
        ),
        tags: ['User'],
        responses: [
            new OA\Response(
                response: 201,
                description: "User created successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "firstName", type: "string", example: "Thomas"),
                        new OA\Property(property: "lastName", type: "string", example: "Dupont"),
                        new OA\Property(property: "pseudo", type: "string", example: "Dudupont"),
                        new OA\Property(property: "email", type: "string", example: "exemple@email.com"),
                        new OA\Property(property: "accountStatus", type: "string", example: "ACTIF"),
                        new OA\Property(property: "createdAt", type: "string", format: "date-time"),
                        new OA\Property(property: "updatedAt", type: "string", format: "date-time")
                    ],
                    type: "object"
                )
            )
        ]
    )]
    public function new(Request $request): JsonResponse
    {
        $user = $this->serializer->deserialize($request->getContent(), User::class, 'json');
        $user->setAccountStatus(1);
        $user->setroles(['ROLE_USER']);
        $user->setCreatedAt(new \DateTimeImmutable());
        $user->setUpdatedAt(new \DateTimeImmutable());

        if (in_array('ROLE_ADMIN', $user->getRoles()) && $this->repository->findAdminUser()) {
            return new JsonResponse(['error' => 'An admin user already exists.'], Response::HTTP_CONFLICT);
        }

        $this->manager->persist($user);
        $this->manager->flush();

        return new JsonResponse(
            $this->serializer->serialize($user, 'json', [AbstractNormalizer::IGNORED_ATTRIBUTES => ['password']]),
            Response::HTTP_CREATED,
            ['Location' => $this->urlGenerator->generate('app_api_user_show', ['id' => $user->getId()])]
        );
    }

    //READ - GET
    #[Route('/{id}', name: 'show', methods: 'GET')]
    #[OA\Get(
        path: '/api/user/{id}',
        summary: 'Show a user by ID',
        tags: ['User'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: "User's ID to show",
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'User found successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 1),
                        new OA\Property(property: 'firstName', type: 'string', example: 'Thomas'),
                        new OA\Property(property: 'lastName', type: 'string', example: 'Dupont'),
                        new OA\Property(property: 'pseudo', type: 'string', example: 'pseudo'),
                        new OA\Property(property: 'email', type: 'string', example: 'exemple@email.com'),
                        new OA\Property(property: 'createdAt', type: 'string', format: 'date-time')
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: 404,
                description: 'User not found'
            )
        ]
    )]
    public function show(int $id): JsonResponse
    {
        $user = $this->repository->findOneBy(['id' => $id]);
        if ($user) {
            return new JsonResponse(
                $this->serializer->serialize($user, 'json', [AbstractNormalizer::IGNORED_ATTRIBUTES => ['password']]),
                Response::HTTP_OK
            );
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    //UPDATE - PUT
    #[Route('/{id}', name: 'edit', methods: 'PUT')]
    #[OA\Put(
        path: '/api/user/{id}',
        summary: 'Update a user by ID',
        requestBody: new OA\RequestBody(
            description: 'Data of the user to update',
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'firstName', type: 'string', example: 'Thomas'),
                    new OA\Property(property: 'lastName', type: 'string', example: 'Dupont'),
                    new OA\Property(property: 'pseudo', type: 'string', example: 'pseudo'),
                    new OA\Property(property: 'email', type: 'string', example: 'exemple@email.com'),
                    new OA\Property(property: 'password', type: 'string', example: 'password')
                ],
                type: 'object'
            )
        ),
        tags: ['User'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: "User's ID to update",
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 204,
                description: 'User updated successfully'
            ),
            new OA\Response(
                response: 404,
                description: 'User not found'
            )
        ]
    )]
    public function edit(int $id, Request $request): JsonResponse
    {
        $user = $this->repository->findOneBy(['id' => $id]);
        if ($user) {
            $this->serializer->deserialize(
                $request->getContent(),
                User::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $user]
            );

            if (in_array('ROLE_ADMIN', $user->getRoles()) && $this->repository->findAdminUser()) {
                return new JsonResponse(['error' => 'An admin user already exists.'], Response::HTTP_CONFLICT);
            }

            $user->setUpdatedAt(new \DateTimeImmutable());
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }


    //DELETE - DELETE
    #[Route('/{id}', name: 'delete', methods: 'DELETE')]
    #[OA\Delete(
        path: '/api/user/{id}',
        summary: 'Delete a user by ID',
        tags: ['User'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: "User's ID to delete",
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 204,
                description: 'User deleted successfully'
            ),
            new OA\Response(
                response: 404,
                description: 'User not found'
            )
        ]
    )]
    public function delete(int $id): JsonResponse
    {
        $user = $this->repository->findOneBy(['id' => $id]);
        if ($user) {
            $this->manager->remove($user);
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
}


