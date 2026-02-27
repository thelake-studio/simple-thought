<?php

namespace App\Form;

use App\Entity\Tag;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Formulario para la creación y edición de Etiquetas (Tags).
 * Permite al usuario definir el nombre y el color identificativo de cada etiqueta.
 */
final class TagType extends AbstractType
{
    /**
     * Construye el formulario añadiendo los campos necesarios.
     *
     * @param FormBuilderInterface $builder El constructor del formulario.
     * @param array $options Opciones adicionales para el formulario.
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nombre de la Etiqueta',
                'attr' => ['placeholder' => 'Ej: Urgente, Importante, Reflexión...']
            ])
            ->add('color', ColorType::class, [
                'label' => 'Color visual',
                'attr' => ['title' => 'Elige un color para esta etiqueta']
            ])
        ;
    }

    /**
     * Configura las opciones por defecto del formulario.
     * Vincula este formulario directamente con la entidad Tag.
     *
     * @param OptionsResolver $resolver El resolutor de opciones de Symfony.
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tag::class,
        ]);
    }
}
