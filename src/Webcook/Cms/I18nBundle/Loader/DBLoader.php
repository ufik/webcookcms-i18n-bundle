<?php

namespace Webcook\Cms\I18nBundle\Loader;

use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\MessageCatalogue;
use Doctrine\Common\Persistence\ObjectManager;

class DBLoader implements LoaderInterface
{
    private $translationsRepository;
 
    /**
     * @param ObjectManager $entityManager
     */
    public function __construct(ObjectManager $entityManager)
    {
        $this->translationsRepository = $entityManager->getRepository("Webcook\Cms\I18nBundle\Entity\Translation");
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
