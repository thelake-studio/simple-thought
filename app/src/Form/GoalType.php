<?php

namespace App\Form;

use App\Entity\Goal;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GoalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nombre del Objetivo',
                'attr' => ['placeholder' => 'Ej: Leer 30 min, Caminar 10k...'],
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Tipo de Objetivo',
                'choices' => [
                    'Racha (Hábito diario/constante)' => Goal::TYPE_STREAK,
                    'Meta Acumulativa (Suma total)' => Goal::TYPE_SUM,
                ],
                'expanded' => true,
                'multiple' => false,
            ])
            ->add('period', ChoiceType::class, [
                'label' => 'Periodicidad',
                'choices' => [
                    'Diario' => Goal::PERIOD_DAILY,
                    'Semanal' => Goal::PERIOD_WEEKLY,
                    'Mensual' => Goal::PERIOD_MONTHLY,
                ],
                'help' => 'Cada cuánto tiempo se reinicia o se mide este objetivo.',
            ])
            ->add('targetValue', IntegerType::class, [
                'label' => 'Meta Numérica (Opcional para Rachas)',
                'required' => false,
                'attr' => ['min' => 1],
                'help' => 'Ej: 20000 (pasos), 1 (vez al día). Déjalo vacío si es solo constancia.',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Goal::class,
        ]);
    }
}
