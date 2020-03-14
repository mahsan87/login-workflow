<?php

namespace App\Form;


use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LoginForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('password')
            ->add('save', SubmitType::class)
            ;
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
           'data_class' => User::class
        ]);
    }
}