<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Person;
use App\PersonType;
use Doctrine\ORM\EntityManagerInterface;

class HomeController extends AbstractController
{
    #[Route('/test')]
    public function index(): Response
    {
        return $this->render('index.html.twig', ["name" => "Sóren"]);
    }

    #[Route(path: '/person', name: 'person_form')]
    public function personForm(Request $request, EntityManagerInterface $em): Response
    {
        $person = new Person();

        $form = $this->createForm(PersonType::class, $person);

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
                'age' => $age . ' años',
                'work' => $person->getWork(),
                'birthDate' => $birthDate->format('Y-m-d'),
                'aceptaComunicaciones' => $person->getAcceptsCommercial(),
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
            $qb->where('p.name LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }
        $qb->setFirstResult($offset)
            ->setMaxResults($limit);

        $people = $qb->getQuery()->getResult();

        $count = $em->getRepository(Person::class)->createQueryBuilder('p');
        if ($search) {
            $count->where('p.name LIKE :search')
                ->setParameter('search', '%' . $search . '%');
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


    #[Route('/{_locale}/api/listado', name: 'person_list_api', requirements: ['_locale' => 'en|es'], methods: ['GET'])]
    public function personListApi(Request $request, EntityManagerInterface $em, string $_locale): Response
    {
        $search = $request->query->get('search', '');
        $page = max(1, (int)$request->query->get('page', 1));
        $limit = 5;
        $offset = ($page - 1) * $limit;

        $qb = $em->getRepository(Person::class)->createQueryBuilder('p');
        if ($search) {
            $qb->where('p.name LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }
        $qb->setFirstResult($offset)
            ->setMaxResults($limit);

        $people = $qb->getQuery()->getResult();

        $count = $em->getRepository(Person::class)->createQueryBuilder('p');
        if ($search) {
            $count->where('p.name LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }
        $total = (int)$count->select('COUNT(p.id)')->getQuery()->getSingleScalarResult();
        $pages = (int)ceil($total / $limit);

        
        $peopleData = [];
        foreach ($people as $person) {
            $today = new \DateTime();
            $birthDate = $person->getBirthDate();
            $age = $today->diff($birthDate)->y;

            $peopleData[] = [
                'id' => $person->getId(),
                'name' => $person->getName(),
                'work' => $person->getWork(),
                'birthDate' => $birthDate->format('Y-m-d'),
                'age' => $age,
                'acceptsCommercial' => $person->getAcceptsCommercial(),
                'isBirthday' => ($birthDate->format('m-d') === $today->format('m-d'))
            ];
        }

        return $this->json([
            'success' => true,
            'data' => [
                'people' => $peopleData,
                'pagination' => [
                    'page' => $page,
                    'pages' => $pages,
                    'total' => $total,
                    'limit' => $limit
                ],
                'search' => $search,
                'locale' => $_locale
            ]
        ]);
    }

    #[Route('/{_locale}/listado', name: 'person_list_locale', requirements: ['_locale' => 'en|es'])]
    public function personListLocale(Request $request, EntityManagerInterface $em, string $_locale): Response
    {

        $search = $request->query->get('search', '');
        $page = max(1, (int)$request->query->get('page', 1));
        $limit = 5;
        $offset = ($page - 1) * $limit;

        $qb = $em->getRepository(Person::class)->createQueryBuilder('p');
        if ($search) {
            $qb->where('p.name LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }
        $qb->setFirstResult($offset)
            ->setMaxResults($limit);

        $people = $qb->getQuery()->getResult();

        $count = $em->getRepository(Person::class)->createQueryBuilder('p');
        if ($search) {
            $count->where('p.name LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }
        $total = (int)$count->select('COUNT(p.id)')->getQuery()->getSingleScalarResult();
        $pages = (int)ceil($total / $limit);

        return $this->render('person_list.html.twig', [
            'people' => $people,
            'search' => $search,
            'page' => $page,
            'pages' => $pages,
            'locale' => $_locale,
        ]);
    }

    #[Route('/person/{id}/edit', name: 'person_edit')]
    public function editPerson(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $person = $em->getRepository(Person::class)->find($id);

        if (!$person) {
            throw $this->createNotFoundException('Persona no encontrada');
        }

        $form = $this->createForm(PersonType::class, $person);

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
