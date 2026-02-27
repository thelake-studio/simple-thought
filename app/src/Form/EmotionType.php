<?php

namespace App\Form;

use App\Entity\Emotion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Formulario para la creación y edición de Emociones.
 * Define los campos y atributos visuales requeridos para interactuar con la entidad Emotion.
 */
final class EmotionType extends AbstractType
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
                'label' => 'Nombre de la emoción',
                'attr' => ['placeholder' => 'Ej: Alegría, Calma...']
            ])
            ->add('value', IntegerType::class, [
                'label' => 'Valor numérico (1-10)',
                'attr' => ['min' => 1, 'max' => 10]
            ])
            ->add('color', ColorType::class, [
                'label' => 'Color representativo',
            ])
            ->add('icon', TextType::class, [
                'label' => 'Icono (FontAwesome)',
                'required' => false,
                'attr' => ['placeholder' => 'fa-smile']
            ])
        ;
    }

    /**
     * Configura las opciones por defecto del formulario.
     * Vincula este formulario directamente con la entidad Emotion.
     *
     * @param OptionsResolver $resolver El resolutor de opciones de Symfony.
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Emotion::class,
        ]);
    }
}
