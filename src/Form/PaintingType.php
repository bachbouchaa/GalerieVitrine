<?php

namespace App\Form;

use App\Entity\Gallery;
use App\Entity\MyPaintingCollection;
use App\Entity\Painting;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;


class PaintingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('artist')
            ->add('creationYear')
            ->add('description')
            ->add('style')
            ->add('myPaintingCollection', EntityType::class, [
                'class' => MyPaintingCollection::class,
                'choice_label' => 'id',
                'disabled' => true, // Disable the field to prevent modifications
                'label' => 'Collection', // Optional: custom label
            ])
            ->add('galleries', EntityType::class, [
                'class' => Gallery::class,
                'choice_label' => 'id',
                'multiple' => true,
                'expanded' => true, // Optional: to display as checkboxes
            ])
            ->add('imageFile', VichImageType::class, [
                'required' => false,
                'label' => 'Painting Image (JPEG or PNG file)',
                'allow_delete' => true, // Permet de supprimer l'image
                'download_uri' => false, // Permet de ne pas afficher le lien de téléchargement
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Painting::class,
        ]);
    }
}
