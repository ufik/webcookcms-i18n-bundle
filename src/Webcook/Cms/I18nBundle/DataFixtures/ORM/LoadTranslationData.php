<?php

/**
 * This file is part of Webcook i18n bundle.
 *
 * See LICENSE file in the root of the bundle. Webcook
 */

namespace Webcook\Cms\I18nBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Webcook\Cms\I18nBundle\Entity\Translation;
use Webcook\Cms\I18nBundle\Entity\Language;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Translation fixtures for tests.
 */
class LoadTranslationData implements FixtureInterface, ContainerAwareInterface, OrderedFixtureInterface
{
    /**
     * System container.
     *
     * @var ContainerInterface
     */
    private $container;

    /**
     * Entity manager.
     *
     * @var ObjectManager
     */
    private $manager;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        $czechLanguage = $manager->getRepository('\Webcook\Cms\I18nBundle\Entity\Language')->findAll()[0];
        $englishLanguage = $manager->getRepository('\Webcook\Cms\I18nBundle\Entity\Language')->findAll()[1];

        $this->addTranslation('common.test.translation', $englishLanguage, 'This is test translation.');
        $this->addTranslation('common.test.translation', $czechLanguage, 'Test prekladace.');
        
        $this->manager->flush();
    }

    private function addTranslation(String $key, Language $language, String $translationText, String $domain = 'messages')
    {
        $translation = new Translation();
        $translation->setKey($key)
                    ->setLanguage($language)
                    ->setCatalogue($domain)
                    ->setTranslation($translationText);

        $this->manager->persist($translation);
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 1;
    }
}
