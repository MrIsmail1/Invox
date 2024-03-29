<?php

namespace App\Controller;

use App\Entity\InvoiceItem;
use App\Entity\Product;
use App\Form\ProductFormType;
use App\Form\SearchAutocompleteProduct;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/products')]
class ProductController extends AbstractController
{
    #[Route('/', name: 'app_product_index', methods: ['GET', 'POST'])]
    public function index(Request $request, ProductRepository $productRepository, PaginatorInterface $paginatorInterface): Response
    {
        $searchForm = $this->createForm(SearchAutocompleteProduct::class);
        $searchForm->handleRequest($request);

        $user = $this->getUser();

        $queryBuilder = $productRepository->createQueryBuilderForUser($user);

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $searchedName = $searchForm->get('name')->getData();

            if ($searchedName) {
                $queryBuilder->where('a.name LIKE :name')
                    ->setParameter('name', '%' . $searchedName . '%');
            }
        }

        $query = $queryBuilder->getQuery();
        $data = $paginatorInterface->paginate(
            $query,
            $request->query->getInt('page', 1),
            15
        );


        $product = new Product();
        $form = $this->createForm(ProductFormType::class, $product);
        $form->handleRequest($request);

        return $this->render('pages/product/page_product_index.html.twig', [
            'productForm' => $form,
            'data' => $data,
            'modal' => "productModal",
            'SearchBar' => $searchForm->createView(),
        ]);
    }

    #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $product = new Product();
        $user = $this->getUser();

        $form = $this->createForm(ProductFormType::class, $product);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $user->addProduct($product);
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pages/product/page_product_new.html.twig', [
            'product' => $product,
            'productForm' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProductFormType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render("pages/product/page_product_edit.html.twig", [
            'product' => $product,
            'ProductForm' => $form,
        ]);
    }

    #[Route('/{id}/{token}', name: 'app_product_delete', methods: ['GET'])]
    public function delete(Request $request, Product $product, string $token, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $product->getId(), $token)) {

            $invoiceItems = $entityManager->getRepository(InvoiceItem::class)->findBy(['product' => $product]);

            if (!empty($invoiceItems)) {

                $this->addFlash('error', 'Product cannot be deleted because it is in use.');

                return $this->redirectToRoute('app_product_index', [
                    'alert' => 'true'
                ], Response::HTTP_SEE_OTHER);
            }

            $entityManager->remove($product);
            $entityManager->flush();

        }

        return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
    }

}
