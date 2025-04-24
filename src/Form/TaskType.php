<?php

namespace App\Form;

use App\Entity\Task;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

/**
 * Форма для создания и редактирования задач
 */
class TaskType extends AbstractType
{
    /**
     * Строит форму с полями для работы с задачами
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Обязательное текстовое поле для названия
            ->add('title', TextType::class, [
                'label' => 'Название задачи'  // Лейбл для отображения
            ])
            
            // Необязательное поле для описания (многострочный текст)
            ->add('description', TextareaType::class, [
                'label' => 'Описание',      // Лейбл для отображения
                'required' => false          // Разрешаем пустое значение
            ])
            
            // Поле даты создания (отображается как единое поле ввода)
            ->add('created_at', null, [
                'widget' => 'single_text',   // Использует HTML5 input[type="datetime-local"]
            ])
        ;
    }

    /**
     * Настраивает параметры формы
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,  // Привязка формы к сущности Task
        ]);
    }
}