<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Form\MovieFormType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\MovieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// class MoviesController extends AbstractController
// {
//     // #[Route('/movies/', name: 'movies')]
//     // public function index(MovieRepository $movieRepository): Response
//     // {
//     //     $movies = $movieRepository->findAll();

//     //     dd($movies);

//     //     return $this->render("index.html.twig");
//     // } Method 1

//     //it's only going to be accessible throughout our movies controller.
//     private $em;

//     // #[Route('/movies/', name: 'movies')]
//     // public function index(EntityManagerInterface $em): Response
//     // {
//     //     $repository = $em->getRepository(Movie::class);
//     //     $movies = $repository->findAll();

//     //     dd($movies);

//     //     return $this->render("index.html.twig");
//     // } //method 2

//     public function __construct(EntityManagerInterface $em) 
//     {
//         $this->em = $em;   
//     }

//     #[Route('/movies/', name: 'movies')]
//     public function index(): Response
//     {
//         //findAll() - SELECT * FROM movies;
//         //find() - SELECT * FROM movies WHERE id = 5;
//         //findBy() - SELECT * FROM movies ORDER BY id DESC
//         //findOneBy() - SELECT * FROM movies WHERE id = 6 AND title = "The Dark Knight" ORDER BY id DESC
//         //count() - SELECT COUNT() FROM movies WHERE id = 1;
//         //getClassName shows that we are currently interacting with our movie entity class
//         $repository = $this->em->getRepository(Movie::class);
//         $movies = $repository->findAll();
//         // $movies = $repository->find(5);
//         // $movies = $repository->findBy([], ["id" => "DESC"]);
//         // $movies = $repository->getClassName();

//         // dd($movies);

//         return $this->render("movies/index.html.twig");
//     } //method with constructor
    
// };


class MoviesController extends AbstractController
{
    private $em;
    public function __construct(MovieRepository $movieRepository, EntityManagerInterface $em )
    {
        $this->movieRepository = $movieRepository;
        $this->em = $em;
    }
    private $movieRepository;

    #[Route("/movies", methods: ['GET'], name: "movies")]
    public function index(): Response
    {
        $movies = $this->movieRepository->findAll();
        // dd($movies);
        return $this->render("movies/index.html.twig", [
            "movies" => $movies
        ]);
    }

    #[Route("/movies/create", name: "create_movie")]
    public function create(HttpFoundationRequest $request): Response
    {
        $movie = new Movie();
        $form = $this->createForm(MovieFormType::class, $movie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newMovie = $form->getData();
            $imagePath= $form->get("imagePath")->getData();
            if ($imagePath) {
                $newFileName = uniqid() . "." . $imagePath->guessExtension();
                try {
                    $imagePath->move(
                        $this->getParameter("kernel.project_dir") . "/public/uploads",
                        $newFileName
                    );
                } catch (FileException $e) {
                    return new Response($e->getMessage());
                }

                $newMovie->setImagePath("/uploads" . $newFileName);
            }

            $this->em->persist($newMovie);
            $this->em->flush();

            return $this->redirectToRoute("movies");
        }
        
        return $this->render("movies/create.html.twig", [
            "form" => $form->createView()
        ]);
    }

    #[Route("/movies/edit/{id}", name:"edit_movie")]
    public function edit($id, HttpFoundationRequest $request): Response
    {
        // dd($id);
        $movie = $this->movieRepository->find($id);
        $form = $this->createForm(MovieFormType::class, $movie);

        $form->handleRequest($request);
        $imagePath = $form->get("imagePath")->getData();

        if ($form->isSubmitted() && $form->isValid()) {
            if ($imagePath) {
                if ($movie->getImagePath() !== null) {
                    if (file_exists(
                        $this->getParameter('kernel.project_dir') . $movie->getImagePath()
                        )) {
                            $this->GetParameter('kernel.project_dir') . $movie->getImagePath();
                    }
                    $newFileName = uniqid() . '.' . $imagePath->guessExtension();

                    try {
                        $imagePath->move(
                            $this->getParameter('kernel.project_dir') . '/public/uploads',
                            $newFileName
                        );
                    } catch (FileException $e) {
                        return new Response($e->getMessage());
                    }

                    $movie->setImagePath('/uploads/' . $newFileName);
                    $this->em->flush();

                    return $this->redirectToRoute('movies');
                }
            } else {
                $movie->setTitle($form->get('title')->getData());
                $movie->setReleaseYear($form->get('releaseYear')->getData());
                $movie->setDescription($form->get('description')->getData());

                $this->em->flush();
                return $this->redirectToRoute('movies');
            }
        }

        return $this->render('movies/edit.html.twig', [
            'movie' => $movie,
            'form' => $form->createView()
        ]);
    }


    #[Route('/movies/{id}', methods: ['GET'], name: 'show_movie')]
    public function show($id): Response
    {
        $movie = $this->movieRepository->find($id);
        
        return $this->render('movies/show.html.twig', [
            'movie' => $movie
        ]);
    }

    
    
};
