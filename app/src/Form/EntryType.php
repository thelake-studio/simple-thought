<?php

namespace App\Form;

use App\Entity\Activity;
use App\Entity\Emotion;
use App\Entity\Entry;
use App\Entity\Tag;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EntryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('content')
            ->add('date')
            ->add('createdAt', null, [
                'widget' => 'single_text',
            ])
            ->add('moodValueSnapshot')
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('emotion', EntityType::class, [
                'class' => Emotion::class,
                'choice_label' => 'id',
            ])
            ->add('activities', EntityType::class, [
                'class' => Activity::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
            ->add('tags', EntityType::class, [
                'class' => Tag::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Entry::class,
        ]);
    }
}
