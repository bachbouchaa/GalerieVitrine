<?php

namespace App\Form;

use App\Entity\Gallery;
use App\Entity\Painting;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GalleryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description')
            ->add('published', CheckboxType::class, [
                'required' => false,
                'label' => 'Published',
            ])
            ->add('paintings', EntityType::class, [
                'class' => Painting::class,
                'choice_label' => 'title',
                'multiple' => true,
                'expanded' => true, // Use checkboxes
                'label' => 'Select Paintings',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Gallery::class,
        ]);
    }
}
