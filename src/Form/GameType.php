<?php

namespace App\Form;

use App\Entity\Game;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class GameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('content')
            ->add('pegi')
            ->add('genre', ChoiceType::class, [
                'choices' => [
                    'Action' => 'Action',
                    'Aventure' => 'Aventure',
                    'FPS' => 'FPS',
                    'MMORPG' => 'MMORPG',
                    'Puzzle' => 'Puzzle',
                    'RPG' => 'RPG',
                    'Sport' => 'Sport',
                    'Stratégie' => 'Strategie',
                    'Simulation' => 'Simulation',
                ],
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('price')
            ->add('promotion')
            ->add('platform', ChoiceType::class, [
                'choices' => [
                    'PC' => 'PC',
                    'PlayStation' => 'PlayStation',
                    'Xbox' => 'Xbox',
                    'Nintendo Switch' => 'Nintendo Switch',
                ],
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('image', TextType::class, [
                'label' => 'Image URL',
                'required' => false,
                'constraints' => [
                    new Url([
                        'message' => 'Please enter a valid URL.',
                    ]),
                ],
            ])
            ->add('release_date', null, [
                'widget' => 'single_text',
            ])
            ->add('created_at', null, [
                'widget' => 'single_text',
            ])
            ->add('updated_at', null, [
                'widget' => 'single_text',
            ])
        ;

        // Add an event listener to handle file upload or URL logic
        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $data = $event->getData();

            /** @var UploadedFile $file */
            $file = $form->get('image')->getData();
            $url = $form->get('image')->getData();

        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Game::class,
        ]);
    }
}
