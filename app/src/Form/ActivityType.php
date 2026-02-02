<?php

namespace App\Form;

use App\Entity\Activity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActivityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nombre de la Actividad',
                'attr' => ['placeholder' => 'Ej: Correr, Leer, Estudiar Symfony...']
            ])
            ->add('category', TextType::class, [
                'label' => 'Categoría',
                'required' => false,
                'attr' => ['placeholder' => 'Ej: Deporte, Ocio, Trabajo']
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Descripción (Opcional)',
                'required' => false,
                'attr' => [
                    'rows' => 3,
                    'placeholder' => 'Breve descripción de lo que implica esta actividad...'
                ]
            ])
            ->add('color', ColorType::class, [
                'label' => 'Color identificativo',
            ])
            ->add('icon', TextType::class, [
                'label' => 'Icono (FontAwesome)',
                'required' => false,
                'attr' => ['placeholder' => 'fa-running']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Activity::class,
        ]);
    }
}
