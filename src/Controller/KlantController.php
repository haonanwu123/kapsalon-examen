<?php

namespace App\Controller;

use App\Entity\Afspraak;
use App\Entity\Order;
use App\Entity\OrderLine;
use App\Entity\Product;
use App\Entity\User;
use App\Form\AfspraakKlantType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class KlantController extends AbstractController
{
    #[Route('/klant/home', name: 'app_klant_home')]
    public function app_klant_home(EntityManagerInterface $entityManager): Response
    {
        $klant = $this->getUser();
        $getAfspraaks = $entityManager->getRepository(Afspraak::class)->findBy(['klant' => $klant]);

        return $this->render('klant/home.html.twig', [
            'getAfspraaks' => $getAfspraaks,
        ]);
    }

    #[Route('/klant/product', name: 'app_klant_product')]
    public function app_klant_product(EntityManagerInterface $entityManager): Response
    {
        $products = $entityManager->getRepository(Product::class)->findAll();

        return $this->render('klant/klantProduct.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/klant/clear/winkelwagen', name: 'app_klant_clear_winkelwagen')]
    public function app_klant_clear_winkelwagen(Request $request): Response
    {
        $order = $request->getSession()->get('order');
        if (!$order) {
            $request->getSession()->clear();
            $this->addFlash('danger', 'winkelwagen leeg!');
        }

        return $this->render('klant/klantProduct.html.twig');
    }

    #[Route('/klant/winkelwagen', name: 'app_klant_winkelwagen')]
    public function app_klant_winkelwagen(EntityManagerInterface $entityManager, Request $request): Response
    {
        $p = $request->getSession()->get('order');
        if (!$p) {
            $this->addFlash('danger', 'Je hebt geen producten');
            return $this->redirectToRoute('app_klant_product');
        }

        $order = new Order();
        $order->setDate(new \DateTime('now'));
        $order->setStatus('In behandeling');

        foreach ($p as $line) {
            $orderline = new OrderLine();
            $product = $entityManager->getRepository(Product::class)->find($line['0']);
            $orderline->setProduct($product);
            $orderline->setAmount($line[1]);
            $entityManager->persist($orderline);
        }

        $entityManager->persist($order);
        $entityManager->flush();
        $request->getSession('order')->clear();
        $this->addFlash('success', 'De bestelling is voltooid');
        return $this->redirectToRoute('app_klant_product');
    }

    #[Route('/klant/contact', name: 'app_klant_contact')]
    public function app_klant_contact(Request $request, EntityManagerInterface $entityManager): Response
    {
        $klantID = $this->getUser();
        $showMedewerker = $entityManager->getRepository(User::class)->findBy(['roles' => array('["ROLE_KAPPER"]')]);

        $addAfspraak = new Afspraak();
        $addAfspraak->setKlant($klantID);

        $form = $this->createForm(AfspraakKlantType::class, $addAfspraak, ['medewerkers' => $showMedewerker]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $afspraak = $form->getData();

            $entityManager->persist($afspraak);
            $entityManager->flush();

            return $this->redirectToRoute('app_klant_home');
        }


        return $this->render('klant/klantContact.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/klant/afspraakUpdate/{id}', name: 'app_klant_afspraak_update')]
    public function app_klant_afspraak_update(EntityManagerInterface $entityManager, Request $request, int $id): Response
    {
        $updateAfspraak = $entityManager->getRepository(Afspraak::class)->find($id);
        $showMedewerker = $entityManager->getRepository(User::class)->findBy(['roles' => array('["ROLE_KAPPER"]')]);

        $form = $this->createForm(AfspraakKlantType::class, $updateAfspraak, [
            'medewerkers' => $showMedewerker
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $updateAfspraak = $form->getData();

            $entityManager->persist($updateAfspraak);
            $entityManager->flush();

            $this->addFlash('warning', 'Afspraak gewijzigd');
            return $this->redirectToRoute('app_klant_home');
        }

        return $this->render('klant/updateAfspraak.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/klant/afspraakDelete/{id}', name: 'app_klant_afspraak_delete')]
    public function app_klant_afspraak_delete(EntityManagerInterface $entityManager, int $id): Response
    {
        $deleteAfspraak = $entityManager->getRepository(Afspraak::class)->find($id);

        $entityManager->remove($deleteAfspraak);
        $entityManager->flush();

        $this->addFlash('danger', 'Afspraak verwijded');
        return $this->redirectToRoute('app_klant_home');
    }
}
