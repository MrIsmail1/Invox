<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\SearchAutocompleteProduct;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/users')]
class UserController extends AbstractController
{
    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    #[Security('is_granted("ROLE_ADMIN")')]
    public function index(UserRepository $userRepository, Request $request, PaginatorInterface $paginatorInterface): Response
    {
        $searchForm = $this->createForm(SearchAutocompleteProduct::class);
        $searchForm->handleRequest($request);

        $queryBuilder = $userRepository->createQueryBuilder('a');

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $searchedLastName = $searchForm->get('user')->getData();

            if ($searchedLastName) {
                $queryBuilder->where('a.lastName LIKE :lastName')
                    ->setParameter('name', '%' . $searchedLastName . '%');
            }
        }

        $query = $queryBuilder->getQuery();
        $data = $paginatorInterface->paginate(
            $query,
            $request->query->getInt('page', 1),
            15
        );

        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        return $this->render('pages/user/page_user_index.html.twig', [
            'userForm' => $form,
            'modal' => 'userModal',
            'data' => $data,
            'SearchBar' => $searchForm->createView(),

        ]);
    }

    /*    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
        public function new(Request $request, EntityManagerInterface $entityManager): Response
        {
            $user = new User();
            $form = $this->createForm(UserType::class, $user);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager->persist($user);
                $entityManager->flush();

                return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('pages/user/new.html.twig', [
                'user' => $user,
                'form' => $form,
            ]);
        }

        #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
        public function show(User $user): Response
        {
            return $this->render('pages/user/show.html.twig', [
                'user' => $user,
            ]);
        }*/

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    #[Security('is_granted("ROLE_ADMIN")')]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pages/user/page_user_edit.html.twig', [
            'user' => $user,
            'UserForm' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
