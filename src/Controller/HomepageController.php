<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\CreateUser;
use App\Form\Login;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class HomepageController extends Controller
{
    private $userRepository;
    private $serializer;
    private $entityManager;

    public function __construct(
        UserRepository $userRepository,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager
    ) {
        $this->userRepository = $userRepository;
        $this->serializer = $serializer;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/", name="login")
     */
    public function index(Request $request): Response
    {
        $form = $this->createForm(Login::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (true === $form->get('submit')->isClicked()) {
                $user = $form->getData();
                $userData = $this->userRepository->findOneBy(
                    [
                        'name' => $user->getName(),
                        'password' => $user->getPassword(),
                    ]
                );

                if ($userData instanceof User) {
                    return $this->redirectToRoute('success');
                }

                return $this->redirectToRoute('login');
            }

            if (true === $form->get('createUser')->isClicked()) {
                return $this->redirectToRoute('createUser');
            }
        }

        return $this->render(
            'home.html.twig',
            [
             'form' => $form->createView()
            ]
        );
    }

    /**
     * @Route("/home", name="success")
     */
    public function success()
    {
        die('in home');
    }

    /**
     * @Route("/createuser", name="createUser")
     */
    public function createUser(Request $request): Response
    {
        $form = $this->createForm(CreateUser::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userData = $form->getData();

            if ($userData instanceof User) {
                $this->entityManager->persist($userData);
                $this->entityManager->flush();

                return $this->redirectToRoute('login');
            }
        }
        return $this->render(
            'createuser.html.twig',
            [
              'form' => $form->createView()
          ]
        );
    }
}
