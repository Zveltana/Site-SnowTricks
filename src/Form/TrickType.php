<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Trick;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => '*Nom :',
                'attr' => [
                    'class' => 'mb-2'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => '*Description :',
                'attr' => [
                    'class' => 'mb-2'
                ]
            ])
            ->add('coverFile', FileType::class, [
                'label' => '*Image de couverture :',
                'attr' => [
                    'class' => 'mb-2'
                ],
            ])
            ->add('pictures', CollectionType::class, [
                'entry_type' => PictureType::class,
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'label' => '*Image(s) :',
                'attr' => [
                    'class' => 'mb-2'
                ]
            ])
            ->add('videos', CollectionType::class, [
                'entry_type' => VideoType::class,
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'required' => false,
                'error_bubbling' => false,
            ])
            ->add('category', EntityType::class, [
                'label' => '*Catégorie :',
                'class' => Category::class,
                'choice_label' => 'name',
                'multiple' => false,
                'expanded' => false,
                'placeholder' => 'Sélectionnez une catégorie',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
