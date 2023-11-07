<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Repository\MovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MoviesController extends AbstractController
{
    // #[Route('/movies/', name: 'movies')]
    // public function index(MovieRepository $movieRepository): Response
    // {
    //     $movies = $movieRepository->findAll();

    //     dd($movies);

    //     return $this->render("index.html.twig");
    // } Method 1

    //it's only going to be accessible throughout our movies controller.
    private $em;

    // #[Route('/movies/', name: 'movies')]
    // public function index(EntityManagerInterface $em): Response
    // {
    //     $repository = $em->getRepository(Movie::class);
    //     $movies = $repository->findAll();

    //     dd($movies);

    //     return $this->render("index.html.twig");
    // } //method 2

    public function __construct(EntityManagerInterface $em) 
    {
        $this->em = $em;   
    }

    #[Route('/movies/', name: 'movies')]
    public function index(): Response
    {
        //findAll() - SELECT * FROM movies;
        //find() - SELECT * FROM movies WHERE id = 5;
        //findBy() - SELECT * FROM movies ORDER BY id DESC
        //findOneBy() - SELECT * FROM movies WHERE id = 6 AND title = "The Dark Knight" ORDER BY id DESC
        //count() - SELECT COUNT() FROM movies WHERE id = 1;
        $repository = $this->em->getRepository(Movie::class);
        // $movies = $repository->findAll();
        // $movies = $repository->find(5);
        // $movies = $repository->findBy([], ["id" => "DESC"]);
        $movies = $repository->getClassName();

        dd($movies);

        return $this->render("index.html.twig");
    } //method with constructor
    
};
