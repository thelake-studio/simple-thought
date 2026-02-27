<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Formulario encargado del registro de nuevos usuarios.
 * Define los campos necesarios para crear una cuenta (email, nickname, contraseña y términos).
 */
final class RegistrationFormType extends AbstractType
{
    /**
     * Construye el formulario añadiendo los campos y sus restricciones de validación.
     *
     * @param FormBuilderInterface $builder El constructor del formulario.
     * @param array $options Opciones adicionales para el formulario.
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Correo Electrónico',
                'attr' => ['placeholder' => 'tu@email.com']
            ])
            ->add('nickname', TextType::class, [
                'label' => '¿Cómo quieres que te llamemos?',
                'attr' => ['placeholder' => 'Tu apodo o nombre']
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label' => 'Acepto los términos y condiciones',
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Debes aceptar nuestros términos para continuar.',
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Contraseña',
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password', 'placeholder' => 'Mínimo 6 caracteres'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor, introduce una contraseña',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Tu contraseña debe tener al menos {{ limit }} caracteres',
                        'max' => 4096, // Máximo permitido por Symfony por razones de seguridad
                    ]),
                ],
            ])
        ;
    }

    /**
     * Configura las opciones por defecto del formulario.
     * Vincula este formulario directamente con la entidad User.
     *
     * @param OptionsResolver $resolver El resolutor de opciones de Symfony.
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
