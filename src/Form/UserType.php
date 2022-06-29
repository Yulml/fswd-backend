<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        if ($options['isSuperadmin']) {
            $choices =[
                'User' => 'ROLE_REGISTERED',
                'Admin' => 'ROLE_ADMIN',
                'Superadmin' => 'ROLE_SUPERADMIN',
            ];
        } else {
            $choices =[
                'User' => 'ROLE_REGISTERED',
            ];
        }



        $builder
            ->add('email')
            ->add('password', PasswordType::class, [
                'required' => false
            ])
            ->add('nickname')
            ->add('dob')
            ->add('avatar');
            if ($options['isSuperadmin']){
                
                $builder->add('roles', ChoiceType::class, [
                'choices' => $choices,
                'expanded' => true,
                'multiple' => true
                ]);
            }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'isSuperadmin' => false,
            'data_class' => User::class,
        ]);
    }
}
