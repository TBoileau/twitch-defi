<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Frequency;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class FrequencyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('unity', TextType::class, [
                'label' => 'Unité',
                'empty_data' => '',
            ])
            ->add('value', IntegerType::class, [
                'label' => 'Valeur',
                'empty_data' => 0,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', Frequency::class);
    }
}
