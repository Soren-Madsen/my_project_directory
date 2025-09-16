<?php

namespace App;

use App\Entity\Person;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('birthDate', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Fecha de nacimiento'
            ])
            ->add('work', ChoiceType::class, [
                'label' => 'Trabajo',
                'choices' => [
                    'Desarrollador' => 'desarrollador',
                    'Diseñador' => 'diseñador',
                    'Profesor' => 'profesor',
                    'Otro' => 'otro'
                ],
                'placeholder' => 'Selecciona una opción'
            ])
            ->add('acceptsCommercial', CheckboxType::class, [
                'label'    => 'Acepto las comunicaciones comerciales',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Person::class,
        ]);
    }
}