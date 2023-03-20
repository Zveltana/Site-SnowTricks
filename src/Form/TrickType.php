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
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Valid;

class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isNew = $options['isNew'];

        $coverConstraints = [
            new Image([
                'mimeTypes' => [
                    'image/png',
                    'image/jpg',
                    'image/jpeg',
                    'image/svg+xml',
                ],
                'mimeTypesMessage' => 'Veuillez insérer une image (png, jpg, jpeg, svg)'
            ])];

        if ($isNew) {
            $coverConstraints[] = new NotNull();
        }

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
                'constraints' => $coverConstraints,
                'required' => $isNew,
            ])
            ->add('pictures', CollectionType::class, [
                'entry_type' => PictureType::class,
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'label' => '*Image(s) :',
                'attr' => [
                    'class' => 'mb-2'
                ],
                'constraints' => [
                    new Valid()
                ],
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
            'isNew' => true, // Set to true by_reference
        ]);
    }
}
