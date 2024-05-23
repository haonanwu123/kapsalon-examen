<?php

namespace App\Controller;

use App\Entity\Afspraak;
use App\Entity\Order;
use App\Entity\OrderLine;
use App\Entity\Product;
use App\Entity\User;
use App\Form\AfspraakKlantType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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

        $orders = $entityManager->getRepository(Order::class)->findBy(['id' => $klant]);

        $orderLines = [];
        foreach ($orders as $order) {
            $orderLines[$order->getId()] = $order->getOrderLines();
        }

        return $this->render('klant/home.html.twig', [
            'getAfspraaks' => $getAfspraaks,
            'orders' => $orders,
            'orderLines' => $orderLines,
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

    #[Route('/klant/makeOrder/{id}', name: 'app_makeorder')]
    public function makeOrder(EntityManagerInterface $em, Request $request, int $id): Response
    {
        $product = $em->getRepository(Product::class)->find($id);
        $form = $this->createFormBuilder()
            ->add('amount', IntegerType::class, [
                'required' => true,
                'data' => 1,
                'label' => 'aantal'
            ])
            ->add('Opslaan', SubmitType::class)
            ->getForm();

        $session = $request->getSession();
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if (!$session->get('order')) {
                $session->set('order', []);
            }
            $amount = $form->get('amount')->getData();
            $order = $session->get('order');
            $order[] = [$id, $amount];
            $session->set('order', $order);

            $this->addFlash('succes', 'product toegevoegd');
            return $this->redirectToRoute('app_klant_home');
        }
        return $this->render('klant/order.html.twig', [
            'product' => $product,
            'form' => $form
        ]);
    }

    #[Route('/klant/winkelwagen',name:'show_winkelwagen')]
    public function showWinkelwagen(EntityManagerInterface $em,Request $request)
    {
        $order=$request->getSession()->get('order');
        if(!$order) {
            $this->addFlash('danger','Je hebt geen producten');
            return $this->redirectToRoute('app_klant_home');
        }

//        $orderlinesLines=[];
        foreach ($order as $line) {
            $orderLine=new OrderLine();
            $product=$em->getRepository(Product::class)->find($line[0]);
            $orderLine->setProduct($product);
            $orderLine->setAmount($line[1]);
            $orderLines[]=$orderLine;
        }
        return $this->render('klant/winkelwagen.html.twig',[
            'orderLines'=>$orderLines,
        ]);
    }

    #[Route('/clear/winkelwagen',name:'clear_winkelwagen')]
    public function clearWinkelwagen(Request $request): Response
    {
        $order=$request->getSession()->get('order');
        if($order) {
            $request->getSession('order')->clear();
            $this->addFlash('danger','winkelwagen leeg!');

        }
        return $this->redirectToRoute('app_klant_home');
    }

    #[Route('/order/winkelwagen',name:'order_winkelwagen')]
    public function orderWinkelwagen(EntityManagerInterface $em,Request $request):Response
    {
        $klant = $this->getUser();
        $product=$request->getSession()->get('order');
        if(!$product) {
            $this->addFlash('danger','Je hebt geen producten');
            return $this->redirectToRoute('app_order');
        }
        $order=new Order();
        $order->setDate(new \DateTime('now'));
        $order->setStatus('In behandeling');
        $order->setKlant($klant);

        foreach ($product as $line) {
            $orderLine=new OrderLine();
            $product=$em->getRepository(Product::class)->find($line[0]);
            $orderLine->setProduct($product);
            $orderLine->setAmount($line[1]);
            $orderLine->setPurchase($order);
            $em->persist($orderLine);
        }

        $em->persist($order);
        $em->flush();
        $request->getSession('order')->clear();
        $this->addFlash('success','De bestelling is voltooid');
        return $this->redirectToRoute('app_klant_home');

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
