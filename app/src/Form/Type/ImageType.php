<?php

namespace App\Form\Type;

use App\Entity\Image;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'title',
            TextType::class,
            [
                'label' => 'label.email',
                'required' => true,
                'attr' => ['max_length' => 255],
            ]);
        $builder->add(
            'description',
            TextareaType::class,
            [
                'label' => 'label.nick',
                'required' => true,
                'attr' => ['max_length' => 255],
            ]);
        $builder->add(
            'path',
            TextType::class,
            [
                'label' => 'label.text',
                'required' => true,
                'attr' => ['max_length' => 255],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Image::class]);
    }

    public function getBlockPrefix(): string
    {
        return 'image';
    }
}
