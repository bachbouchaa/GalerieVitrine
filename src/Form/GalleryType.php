<?php

namespace App\Form;

use App\Entity\Gallery;
use App\Entity\Painting;
use App\Repository\PaintingRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GalleryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $gallery = $options['data'] ?? null;
        $member = $gallery ? $gallery->getMember() : null;
        
        $builder
        ->add('description')
        ->add('published')
        ->add('paintings', EntityType::class, [
            'class' => Painting::class,
            'choice_label' => 'title',
            'multiple' => true,
            'expanded' => true,
            'by_reference' => false,
            // Custom query to restrict paintings to member-owned only
            'query_builder' => function (PaintingRepository $repository) use ($member) {
            return $repository->createQueryBuilder('p')
            ->join('p.myPaintingCollection', 'c')
            ->join('c.member', 'm')
            ->andWhere('m.id = :memberId')
            ->setParameter('memberId', $member->getId());
            },
            ]);
    }
    
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Gallery::class,
        ]);
    }
}
