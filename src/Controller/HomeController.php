<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use App\Entity\Person;
use Symfony\Component\Form\Extension\Core\Type\DateType;




class HomeController extends AbstractController{

    #[Route('/test')]
    public function index(): Response
    {
        return $this->render('index.html.twig', ["name" => "Sóren"]);
    }

#[Route(path: '/person')]
public function personForm(Request $request): Response
{
    $person = new Person();

    $form = $this->createFormBuilder($person)
        ->add('name', TextType::class)
        ->add('birthDate', DateType::class, [
            'widget' => 'single_text',
            'label' => 'Fecha de nacimiento',
        ])
        ->add('work', TextType::class)
        ->add('save', SubmitType::class, ['label' => 'Submit'])
        ->getForm();

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $birthDate = $person->getBirthDate();
        $today = new \DateTime();
        $age = $today->diff($birthDate)->y;

        $session = $request->getSession();
        $session->set('submittedData', [
            'name' => $person->getName(),
            'age' => $age,
            'work' => $person->getWork(),
            'birthDate' => $birthDate->format('Y-m-d'),
        ]);
        return $this->redirectToRoute('person_data');
    }

    return $this->render('form.html.twig', [
        'form' => $form->createView(),
    ]);
}

#[Route(path: '/person_data', name: 'person_data')]
public function personSuccess(Request $request): Response
{
    $session = $request->getSession();
    $submittedData = $session->get('submittedData');

    return $this->render('person_data.html.twig', [
        'submittedData' => $submittedData,
    ]);
}
}

