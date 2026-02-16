<?php

namespace App\Form;

use App\Entity\GoalLog;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GoalLogType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date', DateType::class, [
                'label' => 'Fecha del registro',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
                'input' => 'datetime_immutable',
            ])
            ->add('value', IntegerType::class, [
                'label' => 'Cantidad / Valor',
                'attr' => [
                    'min' => 1,
                    'placeholder' => 'Ej: 1 (para racha) o 5000 (para pasos)'
                ],
                'help' => 'Si es un hábito de racha, pon "1". Si es acumulativo, pon la cantidad exacta.',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GoalLog::class,
        ]);
    }
}
