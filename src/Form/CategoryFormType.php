<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class CategoryFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $parentCategory = $options['data']['parent_category'] ?? null;
        if (is_array($parentCategory)) {
            $choices = [];
            foreach ($parentCategory as $item) {
                $choices[$item->getName()] = $item->getId();
            }
        } else {
            $choices = [
                $parentCategory?->getName() => $parentCategory?->getId(),
            ];
        }

        $builder
            ->add('category_name', TextType::class, [
                'data' => isset($options['data']['category']) ? $options['data']['category']->getName() : null,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter name!',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your name should be more then 6 symbols',
                        'max' => 256,
                        'maxMessage' => 'Your name should not be more then 256 symbols',
                    ]),
                ],
            ])
            ->add('parent', ChoiceType::class, [
                'choices'  => $choices,
            ]);
    }

    public function isValid()
    {

    }
}
