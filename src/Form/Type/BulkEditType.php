<?php

declare(strict_types=1);

/*
 * This file is part of the Asdoria Package.
 *
 * (c) Asdoria .
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Asdoria\SyliusBulkEditPlugin\Form\Type;

use Asdoria\SyliusBulkEditPlugin\Form\EventSubscriber\ConfigurationTypeFormSubscriber;
use Asdoria\SyliusBulkEditPlugin\Form\Type\AbstractFormConfigurableElementType;
use Asdoria\SyliusBulkEditPlugin\Form\Type\BulkEditConfigurationChoiceType;
use Sylius\Bundle\ResourceBundle\Form\Registry\FormTypeRegistryInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class BulkEditType
 * @package Asdoria\SyliusBulkEditPlugin\Form\Type
 *
 * @author  Philippe Vesin <pve.asdoria@gmail.com>
 */
final class BulkEditType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function __construct(
        protected FormTypeRegistryInterface $formTypeRegistry,
        protected ServiceRegistryInterface  $formConfigurationRegistry,
        protected TranslatorInterface       $translator
    )
    {
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options = []): void
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('type', BulkEditConfigurationChoiceType::class, [
                'required'    => true,
                'label'       => 'asdoria_bulk_edit.form.type.header',
                'placeholder' => 'asdoria_bulk_edit.ui.please_selected_item',
                'constraints' => [new Valid([], ['sylius'])],
                'attr'        => [
                    'data-form-collection' => 'update',
                ],
            ])
            ->add('resources', HiddenType::class)
            ->addEventSubscriber(
                new ConfigurationTypeFormSubscriber(
                    $this->formTypeRegistry,
                    $this->formConfigurationRegistry,
                    $this->translator,
                    $options
                )
            );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver
            ->setDefaults([
                'context'           => 'product',
                'validation_groups' => function (FormInterface $form) {
                    $isClicked = $form->has('submit') && $form->get('submit')->isClicked();

                    return $isClicked ? ['sylius'] : [];
                },
            ])
            ->setAllowedTypes('context', ['string']);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'asdoria_bulk_edit_form';
    }
}
