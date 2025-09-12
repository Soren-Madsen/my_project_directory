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

    #[Route(path: '/person', name: 'person')]
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
                'required' => false,
            ])
            ->add('save', SubmitType::class, ['label' => 'Guardar'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($person);
            $em->flush();

            return $this->redirectToRoute('person_data', ['id' => $person->getId()]);
        }

        return $this->render('form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/person_data/{id}', name: 'person_data')]
    public function personSuccess(int $id, EntityManagerInterface $em): Response
    {
        $person = $em->getRepository(Person::class)->find($id);

        if (!$person) {
            throw $this->createNotFoundException('Persona no encontrada');
        }

        $today = new \DateTime();
        $birthDate = $person->getBirthDate();
        $age = $today->diff($birthDate)->y;

        $felicidades = '';
        if ($birthDate->format('m-d') === $today->format('m-d')) {
            $felicidades = '¡Felicidades!!';
        }

        return $this->render('person_data.html.twig', [
            'submittedData' => [
                'name' => $person->getName(),
                'age' => $age,
                'work' => $person->getWork(),
                'birthDate' => $birthDate->format('Y-m-d'),
                'aceptaComunicaciones' => $person->getAcceptsCommercial() ? 'Sí' : 'No',
                'felicidades' => $felicidades,
            ],
        ]);
    }

    #[Route('/listado', name: 'person_list')]
    public function personList(Request $request, EntityManagerInterface $em): Response
    {
        $search = $request->query->get('search', '');
        $page = max(1, (int)$request->query->get('page', 1));
        $limit = 5;
        $offset = ($page - 1) * $limit;

        $qb = $em->getRepository(Person::class)->createQueryBuilder('p');
        if ($search) {
            $qb->where('p.name = :search')
                ->setParameter('search', $search);
        }
        $qb->setFirstResult($offset)
            ->setMaxResults($limit);

        $people = $qb->getQuery()->getResult();

        $count = $em->getRepository(Person::class)->createQueryBuilder('p');
        if ($search) {
            $count->where('p.name = :search')
                ->setParameter('search', $search);
        }
        $total = (int)$count->select('COUNT(p.id)')->getQuery()->getSingleScalarResult();
        $pages = (int)ceil($total / $limit);

        return $this->render('person_list.html.twig', [
            'people' => $people,
            'search' => $search,
            'page' => $page,
            'pages' => $pages,
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

    #[Route('/person/{id}/delete', name: 'person_delete', methods: ['POST'])]
    public function deletePerson(int $id, EntityManagerInterface $em): Response
    {
        $person = $em->getRepository(Person::class)->find($id);
        if ($person) {
            $em->remove($person);
            $em->flush();
        }
        return $this->redirectToRoute('person_list');
    }
}
