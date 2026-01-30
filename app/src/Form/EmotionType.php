<?php

namespace App\Form;

use App\Entity\Emotion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmotionType extends AbstractType
{
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

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Emotion::class,
        ]);
    }
}
