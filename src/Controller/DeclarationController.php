<?php

namespace App\Controller;

use App\Entity\Declaration;
use App\Form\DeclarationType;
use App\Repository\DeclarationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/declaration")
 */
class DeclarationController extends Controller
{

    /**
     * @Route("/", name="declaration_index", methods="GET")
     */
    public function index(Request $request,DeclarationRepository $declarationRepository): Response
    {
        $data = $declarationRepository->findAll();
        $declaration = new Declaration();

        $form = $this->createFormBuilder($declaration)
            ->add('nom', TextType::class)
            ->add('ministre', ChoiceType::class, array(
                'choices'  => array(
                    'Ministère de l\'Intérieur' => 'Ministère de l\'Intérieur',
                    'ministère d\'agriculture' => 'ministère d\'agriculture',
                    'ministre de finance' => 'ministre de finance',
                    'ministre de transport' => 'ministre de transport',
                    'présidence du gouvernement' => 'présidence du gouvernement',
                    'ministre d\'enseignement superieur' => 'ministre d\'enseignement superieur',
                    'ministre education' => 'ministre education'
                ),))
            ->add('date', ChoiceType::class, array(
                'choices'  => array(
                    '2015' => '2015',
                    '2016' => '2016',
                    '2017' => '2017',
                    '2018' => '2018',
                ),))

            ->add('login', SubmitType::class, array('label' => 'chercher'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $data =  $declarationRepository->findOneBy(['nom' => $form->get('nom')->getData()]) ;

        }

        return $this->render('declaration/list.html.twig', ['declarations' => $data,
            'form' => $form->createView()]);
    }

    /**
     * @Route("/old", name="declaration_index", methods="GET")
     */
    public function indexold(DeclarationRepository $declarationRepository): Response
    {
        return $this->render('declaration/index.html.twig', ['declarations' => $declarationRepository->findAll()]);
    }

    /**
     * @Route("/new", name="declaration_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $declaration = new Declaration();
        $form = $this->createForm(DeclarationType::class, $declaration);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($declaration);
            $em->flush();

            return $this->redirectToRoute('security');
        }

        return $this->render('declaration/new.html.twig', [
            'declaration' => $declaration,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="declaration_show", methods="GET")
     */
    public function show(Declaration $declaration): Response
    {
        return $this->render('declaration/show.html.twig', ['declaration' => $declaration]);
    }

    /**
     * @Route("/{id}/edit", name="declaration_edit", methods="GET|POST")
     */
    public function edit(Request $request, Declaration $declaration): Response
    {
        $form = $this->createForm(DeclarationType::class, $declaration);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('declaration_edit', ['id' => $declaration->getId()]);
        }

        return $this->render('declaration/edit.html.twig', [
            'declaration' => $declaration,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="declaration_delete", methods="DELETE")
     */
    public function delete(Request $request, Declaration $declaration): Response
    {
        if ($this->isCsrfTokenValid('delete'.$declaration->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($declaration);
            $em->flush();
        }

        return $this->redirectToRoute('declaration_index');
    }
}
