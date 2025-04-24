<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    /**
     * Найти все задачи (включая удалённые)
     *
     * @return Task[]
     */
    public function findAll(): array
    {
        return parent::findAll();
    }

    /**
     * Найти все задачи, которые не удалены
     *
     * @return Task[]
     */
    public function findAllNotDeleted(): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.deletedAt IS NULL')
            ->getQuery()
            ->getResult();
    }

    /**
     * Построитель запроса для всех не удалённых задач
     *
     * @return QueryBuilder
     */
    public function findAllNotDeletedQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('t')
            ->where('t.deletedAt IS NULL');
    }

    /**
     * Построитель запроса для всех удалённых задач, отсортированных по дате удаления по убыванию
     *
     * @return QueryBuilder
     */
    public function findDeletedTasksQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('t')
            ->where('t.deletedAt IS NOT NULL')
            ->orderBy('t.deletedAt', 'DESC');
    }
}
