<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends Controller
{
    /**
     * @Route("/actualite", name="actualite")
     */
    public function actualite()
    {
        return $this->render('security/actualite.html.twig', [
            'controller_name' => 'SecurityController',
        ]);
    }

    /**
     * @Route("/", name="security")
     */
    public function index()
    {
        return $this->render('security/index.html.twig', [
            'controller_name' => 'SecurityController',
        ]);
    }

    /**
     * @Route("/login", name="login")
     */
    public function login(Request $request, UtilisateurRepository $utilisateurrepository,SessionInterface $session)
    {

        $utilisateur = new Utilisateur();
        $errormail="";
        $errorpassword="";
        $repository = $this->getDoctrine()->getRepository(Utilisateur::class);

        $form = $this->createFormBuilder($utilisateur)
            ->add('email', TextType::class)
            ->add('password', PasswordType::class)
            ->add('login', SubmitType::class, array('label' => 'Se connecter'))
            ->getForm();
        $form->handleRequest($request);





        if ($form->isSubmitted() && $form->isValid()) {
            if( $repository->findBy(['email' => $form->get('email')->getData()]) ){

                $result =  $repository->findOneBy(['email' => $form->get('email')->getData(),'password' => ($form->get('password')->getData()) ]) ;

                if($result){

                            $session->set('user', $result->getId() );
                            return $this->redirectToRoute('declaration_new');

                    }



                }else{
                    $errormail="";
                    $errorpassword="Mot de passe invalide!";
                }
            }else{
                $errormail="Ce email n'existe pas!";
                $errorpassword="";
            }





        return $this->render('security/login.html.twig', [
            'controller_name' => 'SecurityController','errormail' => $errormail,'errorpassword' => $errorpassword,'form' => $form->createView(),
        ]);
    }
}
