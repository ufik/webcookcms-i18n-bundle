<?php

/**
 * This file is part of Webcook security bundle.
 *
 * See LICENSE file in the root of the bundle. Webcook
 */

namespace Webcook\Cms\I18nBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

/**
 * Translation form type.
 */
class TranslationType extends AbstractType
{
    /**
     * {@inheritdoc}
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('key', TextType::class, array(
                'constraints' => array(
                    new NotBlank(array('message' => 'i18n.translations.form.key.required')),
                ),
                'label' => 'i18n.translations.form.key',
            ))
            ->add('catalogue', TextType::class, array(
                'constraints' => array(
                    new NotBlank(array('message' => 'i18n.translations.form.catalogue.required')),
                ),
                'label' => 'i18n.translations.form.catalogue',
            ))
            ->add('translation', TextType::class, array(
                'constraints' => array(
                    new NotBlank(array('message' => 'i18n.translations.form.translation.required')),
                ),
                'label' => 'i18n.translations.form.translation',
            ))
            ->add('language', EntityType::class, array(
                'class' => 'WebcookCmsI18nBundle:Language'
            ))
            ->add('version', HiddenType::class, array('mapped' => false));
    }

    /**
     * {@inheritdoc}
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'      => \Webcook\Cms\I18nBundle\Entity\Translation::class,
            'csrf_protection' => false,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'translation';
    }
}
