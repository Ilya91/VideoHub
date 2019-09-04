<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */

class AdminController extends AbstractController
{
    /**
     * @Route("/my_profile", name="admin_my_profile")
     * @Template("/admin")
     */
    public function index()
    {
        return $this->render('admin/my_profile.html.twig');
    }
}
