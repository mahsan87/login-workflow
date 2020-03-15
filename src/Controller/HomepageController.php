<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\User;
use App\Form\CreateUser;
use App\Form\ImageUpload;
use App\Form\Login;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class HomepageController extends Controller
{
    private $userRepository;
    private $serializer;
    private $entityManager;
    private $directory;

    public function __construct(
        UserRepository $userRepository,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        string $directory
    ) {
        $this->userRepository = $userRepository;
        $this->serializer = $serializer;
        $this->entityManager = $entityManager;
        $this->directory = $directory;
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
                    return $this->redirectToRoute('success', ['id' => $userData->getId()]);
                }

                $this->addFlash(
                    'notice',
                    'Incorrect Login data. User not found!'
                );
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
     * @Route("/home/{id}", name="success")
     */
    public function home(Request $request, $id): Response
    {
        $image = new Image();
        $user = $this->userRepository->findOneBy(['id' => $id]);

        if (null === $user) {
            return $this->redirectToRoute('login');
        }

        $form = $this->createForm(ImageUpload::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $form->get('image')->getData();

            if ($uploadedFile) {
                $fileName = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $fileName);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$uploadedFile->guessExtension();

                // Move the file to the directory where images are stored
                try {
                    $uploadedFile->move(
                        $this->directory,
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                $user->getImage()->add($image->setImageFile($user));
                $image->setName($newFilename);

                $user->addImage($image);

                $this->entityManager->persist($user);
                $this->entityManager->flush();

                $images = $this->getImages($user->getId());

                return $this->render(
                    'image.html.twig',
                    [
                        'form' => $form->createView(),
                        'images' => $images ?? null
                    ]
                );
            }
        }

        $images = $this->getImages($user->getId());
        return $this->render(
            'image.html.twig',
            [
                'form' => $form->createView(),
                'images' => $images ?? null
            ]
        );
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

                return $this->redirectToRoute('login', ['id' => $userData->getId()]);
            }
        }
        return $this->render(
            'createuser.html.twig',
            [
              'form' => $form->createView()
          ]
        );
    }


    private function getImages(int $id): ?array
    {
        $user = $this->userRepository->findOneBy(['id' => $id]);
        if ($user instanceof User) {
            return $user->getImage()->toArray();
        }
        return null;
    }
}
