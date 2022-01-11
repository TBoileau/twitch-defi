<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Scoring;
use App\Entity\ScoringType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ScoringTypeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('label', TextType::class, [
                'label' => 'Intitulé',
                'empty_data' => '',
            ])
            ->add('points', IntegerType::class, [
                'label' => 'Nombre de points',
                'empty_data' => '',
                'help' => 'Doit être supérieur à 0.',
            ])
            ->add('type', EnumType::class, [
                'label' => 'Type',
                'class' => ScoringType::class,
            ])
            ->add('frequency', FrequencyType::class, [
                'label' => null,
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', Scoring::class);
    }
}
