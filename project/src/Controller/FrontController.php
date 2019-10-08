<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Video;
use App\Utils\CategoryTreeFrontPage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends AbstractController
{
    /**
     * @Route("/", name="main")
     */
    public function index()
    {
        return $this->render('front/index.html.twig');
    }

    /**
     * @Route("/videolist/{categoryname}, {id}", name="video_list")
     * @param $id
     * @param CategoryTreeFrontPage $categories
     * @return Response
     */
    public function videoList($id, CategoryTreeFrontPage $categories)
    {
        $videos = $this->getDoctrine()
            ->getRepository(Video::class)
            ->findAll();

        $categories->getCategoryListAndParent($id);
        return $this->render('front/videolist.html.twig',[
            'subcategories' => $categories,
            'videos'=>$videos
        ]);
    }

    /**
     * @Route("/video-details/{id}", name="video_details")
     */
    public function videoDetails()
    {
        $sample_videos = [289729765, 289765765, 247729765, 282329765];
        return $this->render('front/video_details.html.twig', ['sample_videos' => $sample_videos]);
    }

    /**
     * @Route("/search-results", name="search_results")
     */
    public function searchResults()
    {
        return $this->render('front/search_results.html.twig');
    }

    /**
     * @Route("/pricing", name="pricing")
     */
    public function pricing()
    {
        return $this->render('front/pricing.html.twig');
    }

    /**
     * @Route("/register", name="register")
     */
    public function register()
    {
        return $this->render('front/register.html.twig');
    }

    /**
     * @Route("/login", name="login")
     */
    public function login()
    {
        return $this->render('front/login.html.twig');
    }


    /**
     * @return Response
     */
    public function mainCategories(): Response
    {
        $categories = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findBy(['parent'=>null], ['name'=>'ASC']);

        return $this->render('front/_main_categories.html.twig',[
            'categories'=>$categories
        ]);
    }
}
