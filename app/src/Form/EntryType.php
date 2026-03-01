<?php

namespace App\Form;

use App\Entity\Activity;
use App\Entity\Emotion;
use App\Entity\Entry;
use App\Entity\Tag;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Formulario principal para la creación y edición de entradas del diario emocional.
 * Gestiona la inyección de los catálogos personalizados del usuario (emociones, actividades, etiquetas).
 */
final class EntryType extends AbstractType
{
    /**
     * Construye el formulario añadiendo los campos necesarios.
     *
     * @param FormBuilderInterface $builder El constructor del formulario.
     * @param array $options Opciones adicionales pasadas desde el controlador.
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Título (Opcional)',
                'required' => false,
                'attr' => ['placeholder' => 'Resumen del día...']
            ])
            ->add('date', DateType::class, [
                'label' => 'Fecha',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control']
            ])
            ->add('content', TextareaType::class, [
                'label' => '¿Qué tienes en mente?',
                'attr' => ['rows' => 5, 'placeholder' => 'Escribe aquí tus pensamientos, reflexiones...']
            ])
            ->add('emotion', EntityType::class, [
                'class' => Emotion::class,
                'choices' => $options['emotions'],
                'label' => '¿Cómo te has sentido?',
                'choice_label' => 'name',
                'placeholder' => 'Selecciona una emoción...',
            ])
            ->add('activities', EntityType::class, [
                'class' => Activity::class,
                'choices' => $options['activities'],
                'label' => '¿Qué has hecho hoy?',
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true, // Checkboxes
            ])
            ->add('tags', EntityType::class, [
                'class' => Tag::class,
                'choices' => $options['tags'],
                'label' => 'Etiquetas',
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true, // Checkboxes
            ])
        ;
    }

    /**
     * Configura las opciones por defecto del formulario y define las variables personalizadas esperadas.
     *
     * @param OptionsResolver $resolver El resolutor de opciones de Symfony.
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Entry::class,
            'emotions' => [],
            'activities' => [],
            'tags' => [],
        ]);

        $resolver->setAllowedTypes('emotions', 'array');
        $resolver->setAllowedTypes('activities', 'array');
        $resolver->setAllowedTypes('tags', 'array');
    }
}
