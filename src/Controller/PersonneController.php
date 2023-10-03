<?php

namespace App\Controller;

use App\Entity\Personne;
use App\Form\PersonneType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/personne')]
class PersonneController extends AbstractController
{
    #[Route('/',name: 'personne.list')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(Personne::class);

        $personnes = $repository->findAll();
        return $this->render('personne/index.html.twig',['personnes'=>$personnes]);

    }

    #[Route('/alls/age/{ageMin}/{ageMax}',name: 'personne.list.age')]
    public function personneByAge(ManagerRegistry $doctrine, $ageMin, $ageMax): Response
    {
        $repository = $doctrine->getRepository(Personne::class);

        $personnes = $repository->findPersonnesByAgeInterval($ageMin, $ageMax);
        return $this->render('personne/index.html.twig',['personnes'=>$personnes]);

    }

    #[Route('/stats/age/{ageMin}/{ageMax}',name: 'personne.stats.age')]
    public function statsPersonnesByAge(ManagerRegistry $doctrine, $ageMin, $ageMax): Response
    {
        $repository = $doctrine->getRepository(Personne::class);

        $stats = $repository->statsPersonnesByAgeInterval($ageMin, $ageMax);
        return $this->render('personne/stats.html.twig',
            ['stats'=>$stats[0],
               'ageMin'=>$ageMin,
                'ageMax'=>$ageMax
                ]);

    }

    #[Route('/alls/{page?1}/{nbre?12}',name: 'personne.list.alls')]
    public function indexAlls(ManagerRegistry $doctrine, $page, $nbre): Response
    {
        $repository = $doctrine->getRepository(Personne::class);
        $nbPersonne = $repository->count([]);
        $nbrePage = ceil($nbPersonne / $nbre);
        $personnes = $repository->findBy([],[],$nbre, ($page - 1 ) * $nbre);
        return $this->render('personne/index.html.twig',[
            'personnes'=>$personnes,
            'isPaginated'=>true,
            'nbrePage'=>$nbrePage,
            'page'=>$page,
            'nbre'=>$nbre

        ]);

    }

    #[Route('/{id<\d+>}',name: 'personne.detail')]
    public function detail(ManagerRegistry $doctrine, $id): Response
    {
        $repository = $doctrine->getRepository(Personne::class);

        $personne = $repository->find($id);
        if (!$personne)
        {
            $this->addFlash('error',"La personne d'id $id n'existe pas");
            return $this->redirectToRoute('personne.list');
        }
        return $this->render('personne/detail.html.twig',['personne'=>$personne]);

    }

    #[Route('/edit/{id?0}', name: 'personne.edit')]
    public function addPersonne(ManagerRegistry $doctrine, Request $request, $id, SluggerInterface $slugger): Response
    {
        $repository = $doctrine->getRepository(Personne::class);

        $personne = $repository->find($id);

        $new = false;
        if (!$personne){

            $new = true;
            $personne = new Personne();
        }
        $form = $this->createForm(PersonneType::class, $personne);

        $form->remove('createdAt');
        $form->remove('updatedAt');

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $photo = $form->get('photo')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($photo) {
                $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$photo->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $photo->move(
                        $this->getParameter('personne_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $personne->setImage($newFilename);
            }


            $manager = $doctrine->getManager();
            $manager->persist($personne);

            $manager->flush();

            if ($new){
                $message = "a été ajouté avec succes !";
            } else {
                $message = " a été mis à jour avec succes !";
            }

            // Ajoute un message de success
            $this->addFlash('success',$personne->getName().$message);
            return $this->redirectToRoute('personne.list');

        } else {
            return $this->render('personne/add-personne.html.twig', [
                'form' => $form->createView()
            ]);
        }
    }

    #[Route('/delete/{id}', name: 'personne.delete')]
    public function deletePersonne(ManagerRegistry $doctrine, $id): RedirectResponse
    {
        $repository = $doctrine->getRepository(Personne::class);

        $personne = $repository->find($id);
        if ($personne) {
            $manager = $doctrine->getManager();

            // Supprimez la personne de la base de données
            $manager->remove($personne);

            // Exécutez la transaction pour appliquer la suppression
            $manager->flush();

            $this->addFlash('success', 'La personne a été supprimée avec succès !');
        } else {
            $this->addFlash('error', 'La personne est inexistante !');
        }

        // Redirigez vers la page appropriée, par exemple, la liste de toutes les personnes
        return $this->redirectToRoute('personne.list.alls');
    }

    #[Route('/update/{id}/{firstname}/{name}/{age}', name: 'personne.update')]
    public function updatePersonne($id,$firstname,$name,$age, ManagerRegistry $doctrine): Response{

        $repository = $doctrine->getRepository(Personne::class);
        $personne = $repository->find($id);

        if ($personne){
            $personne->setFirstname($firstname);
            $personne->setName($name);
            $personne->setAge($age);

            $manager = $doctrine->getManager();
            $manager->persist($personne);
            $manager->flush();
            $this->addFlash('success', 'La personne a été mis a jour avec succès !');
        } else {
            $this->addFlash('error', 'La personne est inexistante !');
        }

        return $this->redirectToRoute('personne.list.alls');
    }
}
