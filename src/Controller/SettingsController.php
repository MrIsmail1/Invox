<?php

namespace App\Controller;

use App\Form\CompanyDetailsFormType;
use App\Form\UserProfileFormType;
use App\Repository\CompanyDetailsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/settings')]
class SettingsController extends AbstractController
{
    #[Route('/company_details', name: 'app_settings_company_details', methods: ['GET', 'POST'])]
    public function editCompanyDetails(Request $request, EntityManagerInterface $entityManager, CompanyDetailsRepository $companyDetailsRepository): Response
    {

        $companyDetailsId = $this->getUser()->getCompanyDetails()->getId();
        $companyDetails = $companyDetailsRepository->findOneBy(['id' => $companyDetailsId]);

        $form = $this->createForm(CompanyDetailsFormType::class, $companyDetails);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_settings_company_details', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('settings/page_edit_company_details.html.twig', [
            'company_details' => $companyDetails,
            'companyDetailsForm' => $form,
        ]);
    }

    #[Route('/user_profile', name: 'app_settings_user_profile', methods: ['GET', 'POST'])]
    public function editUserProfile(Request $request, EntityManagerInterface $entityManager): Response
    {

        $user = $this->getUser();


        $form = $this->createForm(UserProfileFormType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_settings_user_profile', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('settings/page_edit_user_profile.html.twig', [
            'user' => $user,
            'UserProfileForm' => $form,
        ]);
    }
}
