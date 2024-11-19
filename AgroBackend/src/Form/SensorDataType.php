<?php

namespace App\Form;

use App\Entity\Sensor;
use App\Entity\SensorData;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SensorDataType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nitrogen')
            ->add('phosphorus')
            ->add('potassium')
            ->add('temperature')
            ->add('ph')
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
            'data_class' => SensorData::class,
        ]);
    }
}
