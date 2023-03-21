<?php

namespace App\Controller;


use App\Entity\Etablissement;
use App\Entity\User;
use App\Repository\EtablissementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class EtablissementController extends AbstractController
{
    private EtablissementRepository $etablissementRepository;
    public function __construct(EtablissementRepository $etablissementRepository)
    {
        $this->etablissementRepository = $etablissementRepository;
    }

    #[Route('/etablissements', name: 'app_etablissements')]
    public function getEtablissement(PaginatorInterface $paginator, Request $request): Response
    {
        $etablissements = $paginator->paginate(
            $this->etablissementRepository->findBy([], ['nom' => "ASC"]), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            14 /*limit per page*/
        );

        return $this->render('etablissement/index.html.twig', [
            'etablissements' => $etablissements,
        ]);
    }

    #[Route('etablissements/{slug}', name: 'app_etablissement_slug')]
    public function getEtablissementBySlug($slug): Response
    {
        $etablissement = $this->etablissementRepository->findOneBy(["slug" => $slug]);

        return $this->render('etablissement/etablissement.html.twig', [
            "etablissement" => $etablissement
        ]);
    }

    #[Route('etablissements/favoris/ajouter/{slug}', name: 'app_favori_ajouter')]
    public function ajouterFavori(EntityManagerInterface $entityManager, Security $security, $slug): Response
    {
        $etablissement = $entityManager->getRepository(Etablissement::class)->findOneBy(["slug" => $slug]);
        $userEmail = $security->getUser()->getUserIdentifier();

        $user = $entityManager->getRepository(User::class)->findOneBy(["email" => $userEmail]);
        $user->addFavori($etablissement);

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('app_etablissement_slug', ['slug' => $slug]);
    }

    #[Route('etablissements/favoris/retirer/{slug}', name: 'app_favori_retirer')]
    public function retirerFavori(EntityManagerInterface $entityManager, Security $security, $slug): Response
    {

        $etablissement = $entityManager->getRepository(Etablissement::class)->findOneBy(["slug" => $slug]);
        $userEmail = $security->getUser()->getUserIdentifier();

        $user = $entityManager->getRepository(User::class)->findOneBy(["email" => $userEmail]);
        $user->removeFavori($etablissement);

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('app_etablissement_slug', ['slug' => $slug]);
    }

    #[Route('/etablissements/favoris', name: 'app_etablissements_favoris', priority: 1)]
    public function getEtablissementFavoris(EntityManagerInterface $entityManager, PaginatorInterface $paginator, Request $request): Response
    {

        $favoris = $entityManager->getRepository(User::class)->findOneBy(["email" => $this->getUser()->getUserIdentifier()])->getFavoris();


        $etablissements = $paginator->paginate(
            $favoris,
            $request->query->getInt('page', 1),
            14
        );

        return $this->render('etablissement/index.html.twig', [
            'etablissements' => $etablissements,
        ]);
    }


}