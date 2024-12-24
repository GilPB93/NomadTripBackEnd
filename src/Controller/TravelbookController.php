<?php

namespace App\Controller;

use App\Entity\Travelbook;
use App\Repository\TravelbookRepository;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Annotations as OA;
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
    #[Route('/', name: 'new', methods: ['POST'])]
    /**
     * @OA\Post(
     *     path="/api/travelbook",
     *     summary="Create a travelbook",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Travelbook data to create",
     *         @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="title", type="string", example="Titre du carnet de voyage"),
     *              @OA\Property(property="departureAt", type="string", format="date-time", example="2021-12-31T23:59:59"),
     *              @OA\Property(property="comebackAt", type="string", format="date-time", example="2022-01-01T00:00:00"),
     *              @OA\Property(property="flightNumber", type="string", example="AF1234"),
     *              @OA\Property(property="accommodation", type="string", example="Hôtel"),
     *              @OA\Property(property="createdAt", type="string", format="date-time", example="2021-12-31T23:59:59"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Travelbook created successfully",
     *         @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="id", type="integer", example=1),
     *              @OA\Property(property="title", type="string", example="Titre du travelbook"),
     *              @OA\Property(property="departureAt", type="string", format="date-time"),
     *              @OA\Property(property="comebackAt", type="string", format="date-time"),
     *              @OA\Property(property="flightNumber", type="string"),
     *              @OA\Property(property="accommodation", type="string"),
     *              @OA\Property(property="createdAt", type="string", format="date-time")
     *         )
     *     )
     * )
     */
    public function new(Request $request): JsonResponse
    {
        $travelbook = $this->serializer->deserialize($request->getContent(), Travelbook::class, 'json');
        $this->manager->persist($travelbook);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($travelbook, 'json');
        $location = $this->urlGenerator->generate('app_api_travelbook_show', ['id' => $travelbook->getId()]);

        return new JsonResponse($responseData, Response::HTTP_CREATED, ['Location' => $location], true);
    }


    //READ - GET
    #[Route('/{id}', name: 'show', methods: ['GET'])]
    /**
     * @OA\Get(
     *      path="/api/travelbook/{id}",
     *      summary="Show a travelbook by ID",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID of the travelbook to show",
     *          @OA\Schema(type="integer")
     *      ),
     *     @OA\Response(
     *          response=200,
     *          description="Travelbook found successfully",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="id", type="integer", example=1),
     *              @OA\Property(property="title", type="string", example="Titre du travelbook"),
     *              @OA\Property(property="departureAt", type="string", format="date-time"),
     *              @OA\Property(property="comebackAt", type="string", format="date-time"),
     *              @OA\Property(property="flightNumber", type="string"),
     *              @OA\Property(property="accommodation", type="string"),
     *              @OA\Property(property="createdAt", type="string", format="date-time"),
     *          )
     *      ),
     *     @OA\Response(
     *          response=404,
     *          description="Travelbook not found"
     *      )
     * )
     */
    public function show(int $id): JsonResponse
    {
        $travelbook = $this->repository->find(['id' => $id]);
        if ($travelbook) {
            $responseData = $this->serializer->serialize($travelbook, 'json');
            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }
        return new JsonResponse('Travelbook not found', Response::HTTP_NOT_FOUND);
    }


    //UPDATE - PUT
    #[Route('/{id}', name: 'edit', methods: ['PUT'])]
    /**
     * @OA\Put(
     *      path="/api/travelbook/{id}",
     *      summary="Edit a travelbook by ID",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID of the travelbook to edit",
     *          @OA\Schema(type="integer")
     *      ),
     *     @OA\RequestBody(
     *          required=true,
     *          description="New data of the travelbook to update",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="title", type="string", example="Nouveau titre du travelbook"),
     *              @OA\Property(property="departureAt", type="string", format="date-time", example="2021-12-31T23:59:59"),
     *              @OA\Property(property="comebackAt", type="string", format="date-time", example="2022-01-01T00:00:00"),
     *              @OA\Property(property="flightNumber", type="string", example="AF1234"),
     *              @OA\Property(property="accommodation", type="string", example="Hôtel"),
     *              @OA\Property(property="updatedAt", type="string", format="date-time", example="2021-12-31T23:59:59"),
     *          )
     *      ),
     *     @OA\Response(
     *          response=200,
     *          description="Travelbook updated successfully"
     *      ),
     *     @OA\Response(
     *          response=404,
     *          description="Travelbook not found"
     *      )
     * )
     */
    public function edit(Request $request, int $id): JsonResponse
    {
        $travelbook = $this->repository->find(['id' => $id]);
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
    /**
     * @OA\Delete(
     *      path="/api/travelbook/{id}",
     *      summary="Delete a travelbook by ID",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID of the travelbook to delete",
     *          @OA\Schema(type="integer")
     *      ),
     *     @OA\Response(
     *          response=204,
     *          description="Travelbook deleted successfully"
     *      ),
     *     @OA\Response(
     *          response=404,
     *          description="Travelbook not found"
     *      )
     * )
     */
    public function delete(int $id): JsonResponse
    {
        $travelbook = $this->repository->find(['id' => $id]);
        if ($travelbook) {
            $this->manager->remove($travelbook);
            $this->manager->flush();

            return new JsonResponse('Travelbook deleted', Response::HTTP_NO_CONTENT);
        }
        return new JsonResponse('Travelbook not found', Response::HTTP_NOT_FOUND);
    }
}
