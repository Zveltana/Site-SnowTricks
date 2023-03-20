<?php

namespace App\Form;

use App\Entity\Picture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotNull;

class PictureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('alt')
        ;

        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
               $data = $event->getData();

               $options = [
                   'required' => false,
                   'label' => 'Photo de la figure :',
                   'attr' => [
                       'class' => 'mb-2'
                   ],
                   'constraints' => [
                       new Image([
                           'mimeTypes' => [
                               'image/png',
                               'image/jpg',
                               'image/jpeg',
                               'image/svg+xml',
                           ],
                           'mimeTypesMessage' => 'Veuillez insérer une image (png, jpg, jpeg, svg)'
                       ])
                   ]
               ];

               if ($data === null || $data->getId() === null) {
                   $options["required"] = true;
                   $options["constraints"][] = new NotNull();
               }

               $event->getForm()->add('file', FileType::class, $options);
            });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Picture::class,
        ]);
    }
}
