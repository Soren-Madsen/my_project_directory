<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use App\Entity\Person;

class HomeController extends AbstractController
{
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
            'label' => 'Fecha de nacimiento'
        ])
        ->add('work', ChoiceType::class, [
            'label' => 'Trabajo',
            'choices' => [
                'Desarrollador' => 'desarrollador',
                'Diseñador' => 'diseñador',
                'Profesor' => 'profesor',
                'Otro' => 'otro'
            ],
            'placeholder' => 'Selecciona una opción'
        ])
        ->add('acceptsCommercial', CheckboxType::class, [
            'label'    => 'Acepto las comunicaciones comerciales',
            'required' => false,
            'mapped'   => false,
        ])
        ->add('save', SubmitType::class, ['label' => 'Submit'])
        ->getForm();

    $form->handleRequest($request);

 
    if ($form->isSubmitted() && $form->isValid()) {
        $birthDate = $person->getBirthDate();
        $today = new \DateTime();
        $felicidades = '';
        if ($birthDate && $birthDate->format('m-d') === $today->format('m-d')) {
            $felicidades = 'Felicidades!!';
            $this->addFlash('felicidades', $felicidades);
        }

        $age = $today->diff($birthDate)->y;
        $session = $request->getSession();
        $session->set('submittedData', [
            'name' => $person->getName(),
            'age' => $age,
            'work' => $person->getWork(),
            'birthDate' => $birthDate->format('Y-m-d'),
            'acceptsCommercial' => $form->get('acceptsCommercial')->getData() ? 'Sí' : 'No',
        ]);
        return $this->redirectToRoute('person_data');
    }

    return $this->render('form.html.twig', [
        'form' => $form->createView()
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