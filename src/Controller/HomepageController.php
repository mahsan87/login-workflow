<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\LoginForm;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class HomepageController extends Controller
{
    private $userRepository;
    private $serializer;

    public function __construct(
        UserRepository $userRepository,
        SerializerInterface $serializer
    ) {
        $this->userRepository = $userRepository;
        $this->serializer = $serializer;
    }

    /**
     * @Route("/", name="login")k
     */

    public function index(Request $request): Response
    {
        $form = $this->createForm(LoginForm::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $userData = $this->userRepository->findOneBy(['name' => $user->getName()]);

            if ($userData instanceof User)
            {
                return $this->redirectToRoute('success');
            }

            return $this->redirectToRoute('login');
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
        die('here');
    }
}