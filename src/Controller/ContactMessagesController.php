<?php

namespace App\Controller;

use App\Entity\ContactMessages;
use App\Repository\ContactMessagesRepository;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/contact-messages', name: 'app_api_contact_messages_')]
class ContactMessagesController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private ContactMessagesRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    ){
    }

    //CREATE - POST
    #[Route(name: 'new', methods: 'POST')]
    #[OA\Post(
        path: "/api/contact-messages",
        summary: "Create a new contact message",
        requestBody: new OA\RequestBody(
            description: "Data of the contact message to create",
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Thomas"),
                    new OA\Property(property: "email", type: "string", example: "exemple@email.com"),
                    new OA\Property(property: "subject", type: "string", example: "Subject of the message"),
                    new OA\Property(property: "message", type: "string", example: "message")
                ],
                type: "object"
            )
        ),
        tags: ["Contact Messages"],
        responses: [
            new OA\Response(
            response: 201,
            description: "Contact message created successfully",
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "id", type: "integer", example: 1),
                    new OA\Property(property: "name", type: "string", example: "Thomas"),
                    new OA\Property(property: "email", type: "string", example: "exemple@email.com"),
                    new OA\Property(property: "subject", type: "string", example: "Subject of the message"),
                    new OA\Property(property: "message", type: "string", example: "message"),
                    new OA\Property(property: "sentAt", type: "string", format: "date-time")
                ],
                type: "object"
            )
        )],
    )]
    public function new(Request $request): JsonResponse
    {
        $contactMessage = $this->serializer->deserialize($request->getContent(), ContactMessages::class, 'json');
        $contactMessage->setSentAt(new \DateTimeImmutable());

        $this->manager->persist($contactMessage);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($contactMessage, 'json');
        $location = $this->urlGenerator->generate('app_api_contact_messages_show',
            ['id' => $contactMessage->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );

        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    //READ - GET
    #[Route('/{id}', name: 'show', methods: 'GET')]
    #[OA\Get(
        path: "/api/contact-messages/{id}",
        summary: "Show a contact message by ID",
        tags: ["Contact Messages"],
        parameters: [
            new OA\Parameter(
                name: "id",
                description: "ID of the contact message to show",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Contact message found",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "name", type: "string", example: "Thomas"),
                        new OA\Property(property: "email", type: "string", example: "exemple@email.com"),
                        new OA\Property(property: "subject", type: "string", example: "Subject of the message"),
                        new OA\Property(property: "message", type: "string", example: "message"),
                        new OA\Property(property: "sentAt", type: "string", format: "date-time")
                    ],
                    type: "object"
                )
            ),
            new OA\Response(
                response: 404,
                description: "Contact message not found"
            )
        ]
    )]
    public function show(int $id): JsonResponse
    {
        $contactMessage = $this->repository->findOneBy(['id' => $id]);

        if ($contactMessage){
            $responseData = $this->serializer->serialize($contactMessage, 'json');
            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

}
