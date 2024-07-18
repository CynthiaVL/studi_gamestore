<?php

namespace App\Form;

use App\Entity\Game;
use App\Entity\Order;
use App\Entity\Store;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('order_date', null, [
                'widget' => 'single_text',
            ])
            ->add('pickup_date', null, [
                'widget' => 'single_text',
            ])
            ->add('status')
            ->add('quantity')
            ->add('game', EntityType::class, [
                'class' => Game::class,
                'choice_label' => 'name',
            ])
            ->add('store', EntityType::class, [
                'class' => Store::class,
                'choice_label' => 'name',
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}
