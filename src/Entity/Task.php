<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

// Сущность задачи в системе
#[ORM\Entity(repositoryClass: TaskRepository::class)]
class Task
{
    // ID задачи (автоинкремент)
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Название задачи
    #[ORM\Column(length: 255)]
    private ?string $title = null;

    // Описание задачи
    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    // Дата создания
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    // Статус задачи (new, in_progress, done)
    #[ORM\Column(length: 20)]
    private ?string $status = null;

    #[ORM\Column(name: 'deleted_at', type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $deletedAt = null;

    // Геттеры и сеттеры
    
    /**
     * Возвращает ID задачи
     * @return int|null ID задачи или null если не сохранена
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Возвращает название задачи
     * @return string|null Название задачи
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Устанавливает название задачи
     * @param string $title Новое название задачи
     * @return $this
     */
    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Возвращает описание задачи
     * @return string|null Описание задачи
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Устанавливает описание задачи
     * @param string $description Новое описание
     * @return $this
     */
    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Возвращает дату создания задачи
     * @return \DateTimeInterface|null Дата создания
     */
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    /**
     * Устанавливает дату создания
     * @param \DateTimeInterface $created_at Новая дата создания
     * @return $this
     */
    public function setCreatedAt(\DateTimeInterface $created_at): static
    {
        $this->created_at = $created_at;
        return $this;
    }

    /**
     * Возвращает текущий статус задачи
     * @return string|null Статус (new/in_progress/done)
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * Устанавливает статус задачи
     * @param string $status Новый статус
     * @return $this
     * @throws \InvalidArgumentException Если передан недопустимый статус
     */
    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeInterface $deletedAt): self
    {
        $this->deletedAt = $deletedAt;
        return $this;
    }
    
}