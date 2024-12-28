<?php

namespace App\Controller;

use App\Entity\ActivityLog;
use App\Repository\ActivityLogRepository;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/activity-log', name: 'app_api_activity_log_')]
class ActivityLogController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private ActivityLogRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    ){
    }

    //CREATE - POST
    #[Route(name: 'new', methods: ['POST'])]
    #[OA\Post(
        path: '/api/activity-log',
        summary: 'Create a new activity log',
        requestBody: new OA\RequestBody(
            description: 'Data of the activity log to create',
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'lastLogin', type: 'string', example: 'Date-time of the last login'),
                    new OA\Property(property: 'totalConnectionTime', type: 'string', example: 'Date-time of the total connection\'s time')
                ],
                type: 'object'
            )
        ),
        tags: ['Activity Log'],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Activity log created successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 1),
                        new OA\Property(property: 'lastLogin', type: 'string', example: 'Date-time of the last login'),
                        new OA\Property(property: 'totalConnectionTime', type: 'string', example: 'Date-time of the total connection\'s time')
                    ],
                    type: 'object'
                )
            )
        ]
    )]
    public function new(Request $request) : JsonResponse
    {
        $activityLog = $this->serializer->deserialize($request->getContent(), ActivityLog::class, 'json');
        $activityLog->setCreatedAt(new \DateTimeImmutable());

        $this->manager->persist($activityLog);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($activityLog, 'json');
        $location = $this->urlGenerator->generate('app_api_activity_log_show',
            ['id' => $activityLog->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );

        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);

    }

    //READ - GET
    #[Route('/{id}', name: 'show', methods: ['GET'])]
    #[OA\Get(
        path: '/api/activity-log/{id}',
        summary: 'Show an activity log by ID',
        tags: ['Activity Log'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID of the activity log to show',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Activity log found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 1),
                        new OA\Property(property: 'lastLogin', type: 'string', example: 'Date-time of the last login'),
                        new OA\Property(property: 'totalConnectionTime', type: 'string', example: 'Date-time of the total connection\'s time')
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Activity log not found'
            )
        ]
    )]
    public function show(int $id) : JsonResponse
    {
        $activityLog = $this->repository->findOneBy(['id' => $id]);

        if ($activityLog){
            $responseData = $this->serializer->serialize($activityLog, 'json');
            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }


    //DELETE - DELETE
    #[OA\Delete(
        path: '/api/activity-log/{id}',
        summary: 'Delete an activity log by ID',
        tags: ['Activity Log'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID of the activity log to delete',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 204,
                description: 'Activity log deleted successfully'
            ),
            new OA\Response(
                response: 404,
                description: 'Activity log not found'
            )
        ]
    )]
    public function delete(int $id) : JsonResponse
    {
        $activityLog = $this->repository->findOneBy(['id' => $id]);

        if ($activityLog){
            $this->manager->remove($activityLog);
            $this->manager->flush();
            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

}
