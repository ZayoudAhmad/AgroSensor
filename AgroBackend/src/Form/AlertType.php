<?php

namespace App\Form;

use App\Entity\Alert;
use App\Entity\Sensor;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AlertType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('message')
            ->add('severity')
            ->add('timestamp', null, [
                'widget' => 'single_text',
            ])
            ->add('sensor', EntityType::class, [
                'class' => Sensor::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Alert::class,
        ]);
    }
}
