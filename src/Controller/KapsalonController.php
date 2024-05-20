<?php

namespace App\Controller;

use App\Entity\Behandelingobject;
use App\Entity\Product;
use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class KapsalonController extends AbstractController
{
    #[Route('/', name: 'app_kapsalon')]
    public function index(): Response
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_admin_home');
        }

        if ($this->isGranted('ROLE_KLANT')) {
            return $this->redirectToRoute('app_klant_home');
        }

        if ($this->isGranted('ROLE_KAPPER')) {
            return $this->redirectToRoute('app_kapper_home');
        }

        return $this->render('kapsalon/home.html.twig');
    }

    #[Route('/product', name: 'app_product')]
    public function app_product(EntityManagerInterface $entityManager): Response
    {
        $products = $entityManager->getRepository(Product::class)->findAll();

        return $this->render('kapsalon/product.html.twig',[
            'products' => $products
        ]);
    }

    #[Route('/register', name: 'app_register')]
    public function app_register(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, Request $request): Response
    {
        $klant = new User();

        $klant->setRoles(['ROLE_KLANT']);
        $form = $this->createForm(RegisterType::class, $klant);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $klant = $form->getData();

            $klant->setPassword($passwordHasher->hashPassword(
                $klant,
                $klant->getPassword()
            ));

            $entityManager->persist($klant);
            $entityManager->flush();

            return $this->redirectToRoute('app_klant_home');
        }

        return $this->render('kapsalon/register.html.twig',[
            'form' => $form
        ]);
    }

    #[Route('/behandelingobject', name: 'app_behandelingobject')]
    public function app_behandelingobject(EntityManagerInterface $entityManager): Response
    {
        $behandelingobjects = $entityManager->getRepository(Behandelingobject::class)->findAll();

        return $this->render('kapsalon/behandelingobject.html.twig',[
            'behandelingobjects' => $behandelingobjects
        ]);
    }

    #[Route('/behandeling/{name}', name: 'app_behandeling')]
    public function app_behandeling(EntityManagerInterface $entityManager, string $name): Response
    {
        $boName = $entityManager->getRepository(Behandelingobject::class)->findOneBy(['name' => $name]);
        $behandelings = $boName->getBehandelings();

        return $this->render('kapsalon/behandeling.html.twig',[
            'behandelings' => $behandelings
        ]);
    }
}
