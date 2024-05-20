<?php

namespace App\Controller;

use App\Entity\Afspraak;
use App\Entity\Product;
use App\Entity\User;
use App\Form\AfspraakKlantType;
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
    public function app_klant_home(EntityManagerInterface $entityManager): Response
    {
        $klantID = $this->getUser();
        $showAfspraaks = $entityManager->getRepository(User::class)->find($klantID);
        $getAfspraaks = $showAfspraaks->getAfspraaks();

        return $this->render('klant/home.html.twig', [
            'getAfspraaks' => $getAfspraaks,
        ]);
    }

    #[Route('/klant/contact', name: 'app_klant_contact')]
    public function app_klant_contact(Request $request, EntityManagerInterface $entityManager): Response
    {
        $klantID = $this->getUser();
        $showMedewerker = $entityManager->getRepository(User::class)->findBy(['roles'=>array('["ROLE_KAPPER"]')]);

        $addAfspraak = new Afspraak();
        $addAfspraak->setKlant($klantID);

        $form = $this->createForm(AfspraakKlantType::class, $addAfspraak, [
            'medewerkers' => $showMedewerker
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $afspraak = $form->getData();

            $entityManager->persist($afspraak);
            $entityManager->flush();

            return $this->redirectToRoute('app_klant_home');
        }


        return $this->render('klant/klantContact.html.twig',[
            'form' => $form
        ]);
    }
}
