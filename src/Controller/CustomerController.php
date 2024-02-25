<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Form\CustomerFormType;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\SearchAutocomplete;

#[Route('/customers')]
class CustomerController extends AbstractController
{
    #[Route('/', name: 'app_customer_index', methods: ['GET', 'POST'])]
    public function index(Request $request, CustomerRepository $customerRepository, PaginatorInterface $paginatorInterface): Response
    {
    $searchForm = $this->createForm(SearchAutocomplete::class);
    $searchForm->handleRequest($request);

    $queryBuilder = $customerRepository->createQueryBuilder('a');

    if ($searchForm->isSubmitted() && $searchForm->isValid()) {
        $selectedCustomer = $searchForm->get('customer')->getData();

        if ($selectedCustomer) {
            // Filtrer les clients en fonction du prénom du client sélectionné
            $queryBuilder->where('a.firstName = :firstName')
                         ->setParameter('firstName', $selectedCustomer->getFirstName());
        }
    }

    $query = $queryBuilder->getQuery();
    $data = $paginatorInterface->paginate(
        $query,
        $request->query->getInt('page', 1),
        15
    );
        $customer = new Customer();
        $form = $this->createForm(CustomerFormType::class, $customer);
        $form->handleRequest($request);

        return $this->render('pages/customer/page_customer_index.html.twig', [
            'customerForm' => $form,
            'data' => $data,
            'modal' => "customerModal",
            'SearchBar' => $searchForm->createView(),
        ]);
    }

    #[Route('/new', name: 'app_customer_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $customer = new Customer();
        $form = $this->createForm(CustomerFormType::class, $customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($customer);
            $entityManager->flush();

            return $this->redirectToRoute('app_customer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pages/customer/page_customer_new.html.twig', [
            'customer' => $customer,
            'CustomerForm' => $form,
        ]);
    }

    /*    #[Route('/{id}', name: 'app_customer_show', methods: ['GET'])]
        public function show(Customer $customer): Response
        {
            return $this->render('customer/show.html.twig', [
                'customer' => $customer,
            ]);
        }*/

    #[Route('/{id}/edit', name: 'app_customer_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Customer $customer, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CustomerFormType::class, $customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_customer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('customer/page_customer_edit.html.twig', [
            'customer' => $customer,
            'customerform' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_customer_delete', methods: ['POST'])]
    public function delete(Request $request, Customer $customer, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $customer->getId(), $request->request->get('_token'))) {
            $entityManager->remove($customer);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_customer_index', [], Response::HTTP_SEE_OTHER);
    }
}
