<?php

namespace Webcook\Cms\I18nBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Webcook\Cms\CoreBundle\Base\BasicEntity;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Language entity used for translations and routing.
 *
 * @ORM\Entity
 * @ORM\Table(name="Language")
 * @ApiResource
 */
class Language extends BasicEntity
{
    /** 
     * @ORM\Column(name="title", type="string", length=55) 
     * @Assert\NotBlank
     * @Assert\NotNull
     */
    private $title;

    /** 
     * @ORM\Column(name="locale", type="string", length=2) 
     * @Assert\NotBlank
     * @Assert\NotNull
     */
    private $locale;

    /** @ORM\Column(name="isDefault", type="boolean") */
    private $default = false;

    /**
     * Set locale of a language.
     *
     * @param String $locale locale of a language.
     *
     * @return Language Returns self.
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Get locale.
     *
     * @return String Returns locale of language.
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Set whether language is default or not.
     *
     * @param String $default Default of a language.
     *
     * @return Language Returns self.
     */
    public function setDefault($default)
    {
        $this->default = $default;

        return $this;
    }

    /**
     * Get whether language is default or not.
     *
     * @return boolean Returns if language is default.
     */
    public function isDefault()
    {
        return $this->default;
    }

    /**
     * Gets the value of title.
     *
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the value of title.
     *
     * @param string $title the title
     *
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }
}
