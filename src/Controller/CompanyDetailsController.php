<?php

namespace App\Controller;

use App\Form\CompanyDetailsType;
use App\Repository\CompanyDetailsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/settings/company_details')]
class CompanyDetailsController extends AbstractController
{
    /*#[Route('/', name: 'app_company_details_index', methods: ['GET'])]
    public function index(CompanyDetailsRepository $companyDetailsRepository): Response
    {
        return $this->render('company_details/index.html.twig', [
            'company_details' => $companyDetailsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_company_details_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $companyDetail = new CompanyDetails();
        $form = $this->createForm(CompanyDetailsType::class, $companyDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($companyDetail);
            $entityManager->flush();

            return $this->redirectToRoute('app_company_details_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('company_details/new.html.twig', [
            'company_detail' => $companyDetail,
            'form' => $form,
        ]);
    }*/

    /*#[Route('/{id}', name: 'app_company_details_show', methods: ['GET'])]
    public function show(CompanyDetails $companyDetail): Response
    {
        return $this->render('company_details/show.html.twig', [
            'company_detail' => $companyDetail,
        ]);
    }*/


    #[Route('/', name: 'app_company_details_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, CompanyDetailsRepository $companyDetailsRepository): Response
    {

        $companyDetailsId = $this->getUser()->getCompanyDetails()->getId();
        $companyDetails = $companyDetailsRepository->findOneBy(['id' => $companyDetailsId]);

        $form = $this->createForm(CompanyDetailsType::class, $companyDetails);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_company_details_edit', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('company_details/page_edit_company_details.html.twig', [
            'company_details' => $companyDetails,
            'companyDetailsForm' => $form,
        ]);
    }

    /*#[Route('/{id}', name: 'app_company_details_delete', methods: ['POST'])]
    public function delete(Request $request, CompanyDetails $companyDetail, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $companyDetail->getId(), $request->request->get('_token'))) {
            $entityManager->remove($companyDetail);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_company_details_index', [], Response::HTTP_SEE_OTHER);
    }*/
}
