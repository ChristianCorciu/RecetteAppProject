<?php

namespace App\Form;

use App\Entity\Recipe;
use App\Entity\Category;
use App\Form\RecipeIngredientType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class RecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('difficulty')
            
            // Champs pour les étapes en tant que tableau
            ->add('steps', CollectionType::class, [
                'entry_type' => TextType::class,  // Chaque étape sera un champ de texte
                'entry_options' => ['attr' => ['placeholder' => 'Saisissez une étape de la recette']],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,  // Empêche d'écraser les données liées
            ])
            
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
            ])
            ->add('recipeIngredients', CollectionType::class, [
                'entry_type' => RecipeIngredientType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}
