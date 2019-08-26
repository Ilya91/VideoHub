<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     * @Route("/videolist", name="videolist")
     */
    public function videolist()
    {
        $sample_videos = [289729765, 289765765, 247729765, 282329765];
        return $this->render('front/videolist.html.twig', ['sample_videos' => $sample_videos]);
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
}
