<?php

namespace App\Form;

use App\Entity\GameBuffer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GameBufferType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lang')
            ->add('type')
            ->add('league')
            ->add('team1_name')
            ->add('team2_name')
            ->add('started_at',
                DateTimeType::class,
                [
                    'format' => 'yyyy-MM-dd HH:mm:ss',
                    'widget' => 'single_text'
                ]
            )
            ->add('source');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => GameBuffer::class,
            'csrf_protection' => false,
        ]);
    }
}
