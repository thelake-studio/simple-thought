<?php

namespace App\Form;

use App\Entity\GoalLog;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Formulario para el registro de progresos diarios en los objetivos (Goals).
 * Permite al usuario introducir la fecha y la cantidad o valor numérico de su avance.
 */
final class GoalLogType extends AbstractType
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

    /**
     * Configura las opciones por defecto del formulario.
     * Vincula este formulario directamente con la entidad GoalLog.
     *
     * @param OptionsResolver $resolver El resolutor de opciones de Symfony.
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GoalLog::class,
        ]);
    }
}
