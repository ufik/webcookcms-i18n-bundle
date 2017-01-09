<?php

namespace Webcook\Cms\I18nBundle\Loader;

use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\MessageCatalogue;
use Doctrine\Common\Persistence\ObjectManager;

class DBLoader implements LoaderInterface
{
    private $translationsRepository;

    private $languageRepository;
 
    /**
     * @param ObjectManager $entityManager
     */
    public function __construct(ObjectManager $entityManager)
    {
        $this->translationsRepository = $entityManager->getRepository("Webcook\Cms\I18nBundle\Entity\Translation");
        $this->languageRepository     = $entityManager->getRepository("Webcook\Cms\I18nBundle\Entity\Language");
    }
 
    public function load($resource, $locale, $domain = 'messages')
    {
        // TODO write just one query join on locale
        $language = $this->languageRepository->findOneBy(array(
            'locale' => $locale
        ));

        $translations = $this->translationsRepository->findBy(array(
            'language'  => $language,
            'catalogue' => $domain
        ));
 
        $catalogue = new MessageCatalogue($locale);
 
        foreach ($translations as $translation) {
            $catalogue->set($translation->getKey(), $translation->getTranslation(), $domain);
        }
 
        return $catalogue;
    }
}
