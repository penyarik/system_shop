<?php

namespace App\Form;

use App\CustomEntity\Currency;
use App\CustomEntity\Locale;
use App\Validator\ConstraintFieldSize;
use App\Validator\ConstraintFile;
use App\Validator\ConstraintNumberBetween;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProductFormType extends AbstractType
{
    public const MAX_AMOUNT = 999999999999;
    public const MIN_AMOUNT = 1;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $formData = $options['data']['form_data'] ?? null;

        $builder->add('amount', IntegerType::class, [
                'data' => $formData['amount'] ?? null,
                'constraints' => [new ConstraintNumberBetween(self::MIN_AMOUNT ,self::MAX_AMOUNT)]
            ]);

        $builder->add('image', FileType::class, [
            'label' => 'Gallery photos',
            'multiple' => true,
            'attr'     => [
                'multiple' => 'multiple'
            ],
            'required' => false,
            'constraints' => [
                new ConstraintFile(
                    maxSize: 10240000,
                    extensions: ['png', 'jpg', 'jpeg']
                )
            ],
        ]);
 
        foreach (Currency::cases() as $currency) {
            $builder->add('price_'.strtolower($currency->name), MoneyType::class, [
                'data' => $formData['price_'.strtolower($currency->name)] ?? null,
                'divisor' => 100,
                'currency' => $currency->name,
                'label' => 'Price ',
            ]);
            $builder->add('delivery_cost_'.strtolower($currency->name), MoneyType::class, [
                'data' => $formData['delivery_cost_'.strtolower($currency->name)] ?? null,
                'divisor' => 100,
                'currency' => $currency->name,
                'label' => 'Delivery Cost per 1 item ',
            ]);
            $builder->add('delivery_cost_step_'.strtolower($currency->name), MoneyType::class, [
                'data' => $formData['delivery_cost_step_'.strtolower($currency->name)] ?? null,
                'divisor' => 100,
                'currency' => $currency->name,
                'label' => 'Delivery Cost step for each next item ',
            ]);
        }

        foreach (Locale::cases() as $locale) {
            $builder
                ->add('name_'.strtolower($locale->name), TextType::class, [
                    'data' => $formData['name_'.strtolower($locale->name)] ?? null,
                    'label' => 'Product '. $locale->name. ' Name',
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
                ->add('description_'.strtolower($locale->name), CKEditorType::class, [
                    'data' => $formData['description_'.strtolower($locale->name)] ?? null,
                    'label' => 'Description '. $locale->name. ' Name',
                    'constraints' => [new ConstraintFieldSize(self::MIN_AMOUNT ,self::MAX_AMOUNT)]
            ]);
        }

        $builder->add('country', CountryType::class, [
            'data' => $formData['country'] ?? null,
            'label' => 'Country Product Location'
        ]);
        $builder->add('is_top', CheckboxType::class, [
            'data' => $formData['is_top'] ?? null,
            'label' => 'Top Product', 'required' => false,
            ]);
        $builder->add('is_new', CheckboxType::class, [
            'data' => $formData['is_new'] ?? null,
            'label' => 'Is New', 'required' => false,
            ]);
        $builder->add('attachment', FileType::class, [
            'label' => 'Attachment', 'multiple' => true,
            'attr'     => [
                'multiple' => 'multiple'
            ],
            'required' => false,
            'constraints' => [
                new ConstraintFile(
                    maxSize: 10240000,
                    extensions: ['pdf', 'docx', 'doc', 'xls', 'odt', 'xlsx', 'png', 'jpg', 'jpeg']
                )
            ],
        ]);

        $builder->add('save', SubmitType::class, ['label' => 'Save']);
    }
}
