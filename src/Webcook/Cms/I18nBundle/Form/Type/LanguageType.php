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

/**
 * Language form type.
 */
class LanguageType extends AbstractType
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
            ->add('title', TextType::class, array(
                'constraints' => array(
                    new NotBlank(array('message' => 'common.languages.form.title.required')),
                ),
                'label' => 'common.languages.form.name',
            ))
            ->add('abbr', TextType::class, array(
                'constraints' => array(
                    new NotBlank(array('message' => 'common.languages.form.abbr.required')),
                ),
                'label' => 'common.languages.form.abbr',
            ))->add('default', CheckboxType::class, array(
                'constraints' => array(
                    new NotBlank(array('message' => 'common.languages.form.default.required')),
                ),
                'label' => 'common.languages.form.default',
            ))->add('version', HiddenType::class, array('mapped' => false));
    }

    /**
     * {@inheritdoc}
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => \Webcook\Cms\CoreBundle\Entity\Language::class,
            'csrf_protection'   => false,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'language';
    }
}
