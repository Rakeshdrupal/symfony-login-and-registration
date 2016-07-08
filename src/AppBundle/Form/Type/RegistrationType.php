<?php
namespace AppBundle\Form\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class RegistrationType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username', 'text', ['label'=>'User Name'])
                ->add('password', 'password',['label'=>'Password'])
                ->add('confirm', 'password', ['mapped' => false,'label'=>'Re-type password'])
                ->add('homepage', 'text',['label'=>'Homepage'])
                ->add('email', 'email', ['label'=>'email'])
                ->add('save', 'submit', ['label'=>'Register'])
        ;
    }

    public function getName()
    {
        return 'registration';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\User',
        ]);
    }

}