<?php

namespace Webcook\Cms\I18nBundle\Loader;

use Symfony\Component\Translation\Loader\LoaderInterface;

class DBLoader implements LoaderInterface
{
    private $languageRepository;
 
    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->translationsRepository = $entityManager->getRepository("Webcook\Cms\Entity\Translation");
    }
 
    public function load($resource, $locale, $domain = 'messages')
    {
        $translations = $this->translationsRepository->findBy(array(
            'language.locale' => $locale,
            'catalogue'       => $domain
        ));
 
        $catalogue = new MessageCatalogue($locale);
 
        foreach ($translations as $translation) {
            $catalogue->set($translation->getKey(), $translation->getTranslation(), $domain);
        }
 
        return $catalogue;
    }
}
