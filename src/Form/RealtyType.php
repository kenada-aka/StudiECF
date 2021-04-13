<?php

namespace App\Form;

use App\Entity\Realty;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class RealtyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('rent')
            ->add('to_rent')
            //->add('id_owner', EntityType::class, ['class' => User::class,'choice_label' => 'id'])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Realty::class,
        ]);
    }
}
