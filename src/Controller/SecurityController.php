<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Serializer\SerializerInterface;


#[Route('/api', name: 'app_api_')]
class SecurityController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private SerializerInterface $serializer,
        private UserPasswordHasherInterface $passwordHasher,
    ) {
    }
    //REGISTRATION CONTROLLER
    #[Route('/registration', name: 'registration', methods: 'POST')]
    /**
     * @OA\Post(
     *      path="/api/registration",
     *      summary="Register a new user",
     *      @OA\RequestBody(
     *          required=true,
     *          description="User data to register",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="firstName", type="string", example="Thomas"),
     *              @OA\Property(property="lastName", type="string", example="Dupont"),
     *              @OA\Property(property="pseudo", type="string", example="TotoDupont"),
     *              @OA\Property(property="email", type="string", example="exemple@email.com"),
     *              @OA\Property(property="password", type="string", example="Mot de passe"),
     *              @OA\Property(property='createdAt', type='string', format='date-time', example='2021-09-01T00:00:00+00:00')
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="User registered successfully",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="user", type="string", example="Nom d'utilisateur"),
     *              @OA\Property(property="apiToken", type="string", example="31a023e212f116124a36af14ea0c1c3806eb9378"),
     *              @OA\Property(property="roles", type="array", @OA\Items(type="string", example="ROLE_USER"))
     *    )
     * )
     *
     */
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $user = $this->serializer->deserialize($request->getContent(), User::class, 'json');
        $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPassword()));
        $user->setCreatedAt(new \DateTimeImmutable());

        $this->manager->persist($user);
        $this->manager->flush();

        return new JsonResponse(
            ['user' => $user->getUserIdentifier(), 'apiToken' => $user->getApiToken(), 'roles' => $user->getRoles()],
            Response::HTTP_CREATED
        );
    }


    //LOGIN CONTROLLER
    #[Route('/api/login', name: 'login', methods: 'POST')]
    /**
     * @OA\Post(
     *      path="/api/login",
     *      summary="Login a user",
     *      @OA\RequestBody(
     *          required=true,
     *          description="User data to login",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="email", type="string", example="exemple@email.com"),
     *              @OA\Property(property="password", type="string", example="Mot de passe")
     *          )
     *      ),
     *     @OA\Response(
     *          response=200,
     *          description="User logged in successfully",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="user", type="string", example="Nom d'utilisateur"),
     *              @OA\Property(property="apiToken", type="string", example="31a023e212f116124a36af14ea0c1c3806eb9378"),
     *              @OA\Property(property="roles", type="array", @OA\Items(type="string", example="ROLE_USER"))
     *          )
     *      ),
     *     @OA\Response(
     *     response=401,
     *     description="Invalid credentials"
     *      )
     * )
     */
    public function login(#[CurrentUser] ?User $user): JsonResponse
    {
        if (null === $user) {
            return new JsonResponse(['message' => 'Missing credentials'], Response::HTTP_UNAUTHORIZED);
        }

        return new JsonResponse(
            ['user' => $user->getUserIdentifier(), 'apiToken' => $user->getApiToken(), 'roles' => $user->getRoles()]
        );
    }
}



