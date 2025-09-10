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
use Doctrine\ORM\EntityManagerInterface;

class HomeController extends AbstractController
{
    #[Route('/test')]
    public function index(): Response
    {
        return $this->render('index.html.twig', ["name" => "Sóren"]);
    }


    #[Route(path: '/person')]
    public function personForm(Request $request, EntityManagerInterface $em): Response
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
                'required' => false
            ])
            ->add('save', SubmitType::class, ['label' => 'Submit'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $birthDate = $person->getBirthDate();
            $today = new \DateTime();


            $em->persist($person);
            $em->flush();


            $request->getSession()->set('person_id', $person->getId());
            $request->getSession()->set('acceptsCommercial', $form->get('acceptsCommercial')->getData() ? 'Sí' : 'No');

            return $this->redirectToRoute('person_data');
        }

        return $this->render('form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/listado', name: 'person_list')]
    public function personList(EntityManagerInterface $em): Response
    {
        $people = $em->getRepository(Person::class)->findAll();

        return $this->render('person_list.html.twig', [
            'people' => $people,
        ]);
    }

    #[Route(path: '/person_data', name: 'person_data')]
    public function personSuccess(Request $request, EntityManagerInterface $em): Response
    {
        $personId = $request->getSession()->get('person_id');
        $acceptsCommercial = $request->getSession()->get('acceptsCommercial');

        $person = $em->getRepository(Person::class)->find($personId);

        if (!$person) {
            throw $this->createNotFoundException('Persona no encontrada');
        }

        $today = new \DateTime();
        $birthDate = $person->getBirthDate();
        $age = $today->diff($birthDate)->y;

        return $this->render('person_data.html.twig', [
            'submittedData' => [
                'name' => $person->getName(),
                'age' => $age,
                'work' => $person->getWork(),
                'birthDate' => $birthDate->format('Y-m-d'),
                'acceptsCommercial' => $acceptsCommercial,
            ],
        ]);
    }
    #[Route('/person/{id}/edit', name: 'person_edit')]
public function editPerson(int $id, Request $request, EntityManagerInterface $em): Response
{
    $person = $em->getRepository(Person::class)->find($id);

    if (!$person) {
        throw $this->createNotFoundException('Persona no encontrada');
    }

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
        ])
        ->add('save', SubmitType::class, ['label' => 'Guardar'])
        ->getForm();

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em->flush();
        return $this->redirectToRoute('person_list');
    }

    return $this->render('form.html.twig', [
        'form' => $form->createView()
    ]);
}
}
