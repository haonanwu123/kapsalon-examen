<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class KlantController extends AbstractController
{
    #[Route('/klant/home', name: 'app_klant_home')]
    public function app_klant_home(): Response
    {
        return $this->render('klant/home.html.twig');
    }
}
