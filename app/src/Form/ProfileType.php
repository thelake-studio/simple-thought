<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Formulario para la edición del perfil de usuario.
 * Permite actualizar la información básica de la cuenta, como el apodo (nickname) y el correo electrónico.
 */
final class ProfileType extends AbstractType
{
    /**
     * Construye el formulario añadiendo los campos necesarios.
     *
     * @param FormBuilderInterface $builder El constructor del formulario.
     * @param array $options Opciones adicionales pasadas al formulario.
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nickname', TextType::class, [
                'label' => 'Tu Nickname',
                'attr' => ['class' => 'form-control']
            ])
            ->add('email', EmailType::class, [
                'label' => 'Correo Electrónico',
                'attr' => ['class' => 'form-control']
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
