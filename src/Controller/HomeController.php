<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    /**
     * Homepage
     * @return Response
     */
    #[Route("/", name: "home")]
    public function home(): Response
    {
        return $this->render("home.html.twig");
    }
}