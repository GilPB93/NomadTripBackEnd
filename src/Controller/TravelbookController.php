<?php

namespace App\Controller;

use App\Entity\Travelbook;
use App\Repository\TravelbookRepository;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;


#[Route('/api/travelbook', name: 'app_api_travelbook_')]
class TravelbookController extends AbstractController
{

    public function __construct(
        private TravelbookRepository $travelbookRepository,
        private EntityManagerInterface $manager,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    ){
    }

    // CREATE A NEW TRAVELBOOK


    #[Route(name: 'new', methods: ['POST', 'OPTIONS'])]
    #[OA\Post(
        path: "/api/travelbook",
        summary: "Create a new travelbook",
        requestBody: new OA\RequestBody(
            description: "Travelbook object that needs to be added",
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "title", type: "string", example: "My trip to Paris"),
                    new OA\Property(property: "imgCouverture", description: "URL of the image in the cover of the travelbook", type: "string", example: "imgCouvertureFile=@/chemin/vers/image.jpg"),
                    new OA\Property(property: "departureAt", type: "string", example: "2022-12-01"),
                    new OA\Property(property: "comebackAt", type: "string", example: "2022-12-10"),
                    new OA\Property(property: "flightNumber", type: "string", example: "AF1234"),
                    new OA\Property(property: "accommodation", type: "string", example: "Hotel"),
                    new OA\Property(property: "user", type: "integer", example: 1),
                ]
            )
        ),
        tags: ["Travelbook"],
        responses: [
            new OA\Response(
                response: "201",
                description: "Travelbook created",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "title", type: "string", example: "My trip to Paris"),
                        new OA\Property(property: "imgCouverture", description: "URL of the image in the cover of the travelbook", type: "string", example: "imgCouvertureFile=@/chemin/vers/image.jpg"),
                        new OA\Property(property: "departureAt", type: "string", example: "2022-12-01"),
                        new OA\Property(property: "comebackAt", type: "string", example: "2022-12-10"),
                        new OA\Property(property: "flightNumber", type: "string", example: "AF1234"),
                        new OA\Property(property: "accommodation", type: "string", example: "Hotel"),
                        new OA\Property(property: "createdAt", type: "string", example: "2022-12-01T00:00:00+00:00"),
                        new OA\Property(property: "updatedAt", type: "string", example: "2022-12-01T00:00:00+00:00"),
                        new OA\Property(property: "user", type: "integer", example: 1),
                    ]
                )
            ),
            new OA\Response(
                response: "400",
                description: "Invalid input",
            )
        ]
    )]
    public function new(Request $request, #[CurrentUser] $user): JsonResponse
    {
        if ($request->getMethod() === "OPTIONS") {
            return new JsonResponse(null, Response::HTTP_OK, [
                'Access-Control-Allow-Origin' => 'http://localhost:3000',
                'Access-Control-Allow-Methods' => 'GET, POST, OPTIONS, PUT, DELETE',
                'Access-Control-Allow-Headers' => 'Content-Type, Authorization, X-AUTH-TOKEN',
            ]);
        }

        try {
            // 🔹 Vérification des données reçues
            dump("Données reçues :", $request->request->all());
            dump("Fichiers reçus :", $request->files->all());

            // 🔹 Création d'un Travelbook
            $travelbook = new Travelbook();
            $travelbook->setTitle($request->request->get('title', ''));
            $travelbook->setDepartureAt(new \DateTimeImmutable($request->request->get('departureAt', '')));
            $travelbook->setComebackAt(new \DateTimeImmutable($request->request->get('comebackAt', '')));
            $travelbook->setFlightNumber($request->request->get('flightNumber', null));
            $travelbook->setAccommodation($request->request->get('accommodation', null));
            $travelbook->setCreatedAt(new \DateTimeImmutable());
            $travelbook->setUpdatedAt(new \DateTimeImmutable());
            $travelbook->setUser($user);

            // 🔹 Récupération du fichier image
            $file = $request->files->get('imgCouverture');
            if ($file instanceof UploadedFile) {
                $travelbook->setImgCouvertureFile($file);
            } else {
                return new JsonResponse(
                    ["error" => "Aucun fichier reçu"],
                    Response::HTTP_BAD_REQUEST,
                    ['Access-Control-Allow-Origin' => 'http://localhost:3000']
                );
            }

            // 🔹 Sauvegarde en base de données
            $this->manager->persist($travelbook);
            $this->manager->flush();

            return new JsonResponse(
                $this->serializer->serialize($travelbook, 'json', ['groups' => 'travelbook:read']),
                Response::HTTP_CREATED,
                ['Access-Control-Allow-Origin' => 'http://localhost:3000'],
                true
            );
        } catch (\Exception $e) {
            return new JsonResponse(
                ["error" => "Erreur serveur : " . $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR,
                ['Access-Control-Allow-Origin' => 'http://localhost:3000']
            );
        }

    }


    // SHOW ONE TRAVELBOOK
    #[Route('/{id}', name: 'show', methods: ['GET'])]
    #[OA\Get(
        path: "/api/travelbook/{id}",
        summary: "Show one travelbook",
        tags: ["Travelbook"],
        parameters: [
            new OA\Parameter(
                name: "id",
                description: "ID of the travelbook to return",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: "200",
                description: "Travelbook found",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "title", type: "string", example: "My trip to Paris"),
                        new OA\Property(property: "imgCouverture", description: "URL of the image in the cover of the travelbook", type: "string", example: "imgCouvertureFile=@/chemin/vers/image.jpg"),
                        new OA\Property(property: "departureAt", type: "string", example: "2022-12-01"),
                        new OA\Property(property: "comebackAt", type: "string", example: "2022-12-10"),
                        new OA\Property(property: "flightNumber", type: "string", example: "AF1234"),
                        new OA\Property(property: "accommodation", type: "string", example: "Hotel"),
                        new OA\Property(property: "createdAt", type: "string", example: "2022-12-01T00:00:00+00:00"),
                        new OA\Property(property: "updatedAt", type: "string", example: "2022-12-01T00:00:00+00:00"),
                        new OA\Property(property: "user", type: "integer", example: 1),
                    ]
                )
            ),
            new OA\Response(
                response: "404",
                description: "Travelbook not found",
            )
        ]
    )]
    public function getTravelbook(int $id): Response
    {
        $travelbook = $this->travelbookRepository->find($id);

        if (!$travelbook) {
            return new JsonResponse(['message' => 'Travelbook not found'], Response::HTTP_NOT_FOUND);
        }

        $travelbookData = $this->serializer->normalize($travelbook, 'json', ['groups' => ['travelbook:read']]);

        if ($travelbook->getImgCouverture()) {
            $travelbookData['imgCouverture'] = $this->urlGenerator->generate(
                    'app_api_travelbook_show',
                    ['id' => $travelbook->getId()],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ) . '/uploads/travelbookscover/' . $travelbook->getImgCouverture();
        }

        return new JsonResponse($travelbookData, Response::HTTP_OK);
    }


    // EDIT A TRAVELBOOK
    #[Route('/{id}', name: 'edit', methods: ['PUT'])]
    #[OA\Put(
        path: "/api/travelbook/{id}",
        summary: "Edit a travelbook",
        requestBody: new OA\RequestBody(
            description: "Travelbook object that needs to be edited",
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "title", type: "string", example: "My trip to Paris"),
                    new OA\Property(property: "imgCouverture", description: "URL of the image in the cover of the travelbook", type: "string", example: "imgCouvertureFile=@/chemin/vers/image.jpg"),
                    new OA\Property(property: "departureAt", type: "string", example: "2022-12-01"),
                    new OA\Property(property: "comebackAt", type: "string", example: "2022-12-10"),
                    new OA\Property(property: "flightNumber", type: "string", example: "AF1234"),
                    new OA\Property(property: "accommodation", type: "string", example: "Hotel"),
                    new OA\Property(property: "user", type: "integer", example: 1),
                ]
            )
        ),
        tags: ["Travelbook"],
        parameters: [
            new OA\Parameter(
                name: "id",
                description: "ID of the travelbook to edit",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: "200",
                description: "Travelbook updated",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "title", type: "string", example: "My trip to Paris"),
                        new OA\Property(property: "imgCouverture", description: "URL of the image in the cover of the travelbook", type: "string", example: "imgCouvertureFile=@/chemin/vers/image.jpg"),
                        new OA\Property(property: "departureAt", type: "string", example: "2022-12-01"),
                        new OA\Property(property: "comebackAt", type: "string", example: "2022-12-10"),
                        new OA\Property(property: "flightNumber", type: "string", example: "AF1234"),
                        new OA\Property(property: "accommodation", type: "string", example: "Hotel"),
                        new OA\Property(property: "createdAt", type: "string", example: "2022-12-01T00:00:00+00:00"),
                        new OA\Property(property: "updatedAt", type: "string", example: "2022-12-01T00:00:00+00:00"),
                        new OA\Property(property: "user", type: "integer", example: 1),
                    ]
                )
            ),
            new OA\Response(
                response: "404",
                description: "Travelbook not found",
            )
        ]
    )]
    public function edit(int $id, Request $request): Response
    {
        $travelbook = $this->travelbookRepository->findOneBy(['id' => $id]);

        if (!$travelbook) {
            return new JsonResponse(
                ['message' => 'Travelbook not found'],
                Response::HTTP_NOT_FOUND
            );
        }

        $this->serializer->deserialize($request->getContent(), Travelbook::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $travelbook]);
        $travelbook->setUpdatedAt(new \DateTimeImmutable());
        $file = $request->files->get('imgCouverture');
        if ($file) {
            $travelbook->setImgCouvertureFile($file);
        }

        $this->manager->flush();

        return new JsonResponse(
            $this->serializer->serialize($travelbook, 'json', ['groups' => 'travelbook:read']),
            Response::HTTP_OK
        );
    }


    // DELETE A TRAVELBOOK
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    #[OA\Delete(
        path: "/api/travelbook/{id}",
        summary: "Delete a travelbook by ID",
        tags: ["Travelbook"],
        parameters: [
            new OA\Parameter(
                name: "id",
                description: "ID of the travelbook to delete",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: "204",
                description: "Travelbook deleted",
            ),
            new OA\Response(
                response: "404",
                description: "Travelbook not found",
            )
        ]
    )]
    public function delete(int $id): Response
    {
        $travelbook = $this->travelbookRepository->findOneBy(['id' => $id]);

        if (!$travelbook) {
            return new JsonResponse(
                ['message' => 'Travelbook not found'],
                Response::HTTP_NOT_FOUND
            );
        }

        $filesystem = new Filesystem();
        if ($travelbook->getImgCouverture()) {
            $imagePath = $this->getParameter('kernel.project_dir') . '/public/uploads/travelbookscover/' . $travelbook->getImgCouverture();
            if ($filesystem->exists($imagePath)) {
                $filesystem->remove($imagePath);
            }
        }

        $this->manager->remove($travelbook);
        $this->manager->flush();

        return new JsonResponse(
            ['message' => 'Travelbook deleted'],
            Response::HTTP_NO_CONTENT
        );
    }
}
