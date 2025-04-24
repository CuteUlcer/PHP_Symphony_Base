<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/task')]
class TaskController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly TaskRepository $taskRepository
    ) {}

    #[Route('', name: 'app_task_index', methods: ['GET'])]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $queryBuilder = $this->taskRepository->findAllNotDeletedQueryBuilder();

        // Фильтрация по статусу
        $status = $request->query->get('status');
        if ($status && in_array($status, ['new', 'in_progress', 'done'])) {
            $queryBuilder->andWhere('t.status = :status')
                ->setParameter('status', $status);
        }

        // Поиск
        $search = $request->query->get('search');
        if ($search && is_string($search)) {
            $queryBuilder->andWhere('t.title LIKE :search OR t.description LIKE :search')
                ->setParameter('search', '%'.addcslashes($search, '%_').'%');
        }

        // Сортировка
        $sort = $request->query->get('sort', 'created_at');
        $direction = $request->query->get('direction', 'DESC');
        
        if (in_array($sort, ['title', 'status', 'created_at']) && 
            in_array(strtoupper($direction), ['ASC', 'DESC'])) {
            $queryBuilder->orderBy('t.'.$sort, $direction);
        } else {
            $queryBuilder->orderBy('t.created_at', 'DESC');
        }

        $pagination = $paginator->paginate(
            $queryBuilder->getQuery(),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('task/index.html.twig', [
            'pagination' => $pagination,
            'currentStatus' => $status,
            'searchQuery' => $search,
            'sort' => $sort,
            'direction' => $direction,
        ]);
    }

    #[Route('/deleted', name: 'app_task_deleted', methods: ['GET'])]
    public function deleted(Request $request, PaginatorInterface $paginator): Response
    {
        $queryBuilder = $this->taskRepository->findDeletedTasksQueryBuilder();
        
        $pagination = $paginator->paginate(
            $queryBuilder->getQuery(),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('task/deleted.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/new', name: 'app_task_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task->setCreatedAt(new \DateTimeImmutable());
            
            $this->entityManager->persist($task);
            $this->entityManager->flush();

            $this->addFlash('success', 'Задача успешно создана!');
            return $this->redirectToRoute('app_task_index');
        }

        return $this->render('task/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_task_show', methods: ['GET'])]
    public function show(Task $task): Response
    {
        if ($task->getDeletedAt()) {
            throw $this->createNotFoundException('Задача не найдена или была удалена');
        }

        return $this->render('task/show.html.twig', [
            'task' => $task,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_task_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Task $task): Response
    {
        if ($task->getDeletedAt()) {
            throw $this->createNotFoundException('Задача не найдена или была удалена');
        }

        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            $this->addFlash('success', 'Задача успешно обновлена!');
            return $this->redirectToRoute('app_task_index');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form,
            'task' => $task,
        ]);
    }

    #[Route('/{id}', name: 'app_task_delete', methods: ['POST'])]
    public function delete(Request $request, Task $task): Response
    {
        if ($this->isCsrfTokenValid('delete'.$task->getId(), $request->request->get('_token'))) {
            $task->setDeletedAt(new \DateTime());
            $this->entityManager->flush();
            $this->addFlash('success', 'Задача перемещена в корзину');
        }

        return $this->redirectToRoute('app_task_index');
    }

    #[Route('/{id}/restore', name: 'app_task_restore', methods: ['POST'])]
    public function restore(Request $request, Task $task): Response
    {
        if (!$task->getDeletedAt()) {
            throw $this->createNotFoundException('Задача не была удалена');
        }

        if ($this->isCsrfTokenValid('restore'.$task->getId(), $request->request->get('_token'))) {
            $task->setDeletedAt(null);
            $this->entityManager->flush();
            $this->addFlash('success', 'Задача успешно восстановлена!');
        }

        return $this->redirectToRoute('app_task_deleted');
    }
}