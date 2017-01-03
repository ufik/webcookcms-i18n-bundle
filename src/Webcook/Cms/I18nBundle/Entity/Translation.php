<?php

namespace Webcook\Cms\I18nBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Webcook\Cms\CoreBundle\Base\BasicEntity;

/**
 * Translation entity used for translations of strings.
 *
 * @ORM\Entity
 * @ORM\Table(name="Translation")
 */
class Translation extends BasicEntity
{
    /** @ORM\Column(name="key", type="string", length=128) */
    private $key;

    /** @ORM\Column(name="catalogue", type="string", length=128) */
    private $catalogue;

    /** @ORM\ManyToOne(targetEntity="Language") */
    private $language;

    /** @ORM\Column(name="translation", type="text") */
    private $translation;

    /**
     * Gets the value of key.
     *
     * @return mixed
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Sets the value of key.
     *
     * @param mixed $key the key
     *
     * @return self
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Gets the value of language.
     *
     * @return mixed
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Sets the value of language.
     *
     * @param mixed $language the language
     *
     * @return self
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Gets the value of translation.
     *
     * @return mixed
     */
    public function getTranslation()
    {
        return $this->translation;
    }

    /**
     * Sets the value of translation.
     *
     * @param mixed $translation the translation
     *
     * @return self
     */
    public function setTranslation($translation)
    {
        $this->translation = $translation;

        return $this;
    }

    /**
     * Gets the value of catalogue.
     *
     * @return mixed
     */
    public function getCatalogue()
    {
        return $this->catalogue;
    }

    /**
     * Sets the value of catalogue.
     *
     * @param mixed $catalogue the catalogue
     *
     * @return self
     */
    public function setCatalogue($catalogue)
    {
        $this->catalogue = $catalogue;

        return $this;
    }
}
