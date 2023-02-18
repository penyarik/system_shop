<?php

namespace App\Form;

use App\CustomEntity\Locale;
use App\Validator\ConstraintFieldSize;
use App\Validator\ConstraintFile;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
            $choices['No Parent'] = null;
            foreach ($parentCategory as $item) {
                $choices[$item->getName()] = $item->getId();
            }
            $defaultParent = null;
        } else {
            $choices = [
                $parentCategory?->getName() => $parentCategory?->getId(),
            ];

            $defaultParent = $parentCategory?->getId();
        }

        $builder->add('icon', FileType::class, [
            'label' => 'Category Icon',
            'multiple' => false,
            'required' => !$options['data']['is_update'],
            'constraints' => [
                new ConstraintFile(
                    maxSize: 102400,
                    extensions: ['png', 'jpg', 'jpeg']
                )
            ],
        ]);

        $builder->add('image', FileType::class, [
            'label' => 'Category Image',
            'multiple' => false,
            'required' => !$options['data']['is_update'],
            'constraints' => [
                new ConstraintFile(
                    maxSize: 102400,
                    extensions: ['png', 'jpg', 'jpeg']
                )
            ],
        ]);

        $builder
            ->add('parent', ChoiceType::class, [
                'choices'  => $choices,
                'data' => $defaultParent,
            ]);

        foreach (Locale::cases() as $locale) {
            $builder
                ->add('name_'.strtolower($locale->name), TextType::class, [
                    'data' => $options['data']['name_'.strtolower($locale->name)] ?? null,
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
                    'label' => 'Category Name '. $locale->name. ' Name',
                ])
                ->add('description_'.strtolower($locale->name), CKEditorType::class, [
                    'data' => $options['data']['description_'.strtolower($locale->name)] ?? null,
                    'label' => 'Description '. $locale->name. ' Name',
                    'constraints' => [new ConstraintFieldSize(1 ,80000)],
                    'config'      => array('uiColor' => '#ffffff'),
                ]);
        }

        $builder->add('save', SubmitType::class, ['label' => 'Save']);
    }

    public function isValid()
    {

    }
}
