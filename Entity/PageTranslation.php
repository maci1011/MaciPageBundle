<?php

namespace Maci\PageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PageTranslation
 */
class PageTranslation implements \A2lix\I18nDoctrineBundle\Doctrine\Interfaces\OneLocaleInterface
{
    use \A2lix\I18nDoctrineBundle\Doctrine\ORM\Util\Translation;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $subtitle;

    /**
     * @var text
     */
    private $description;

    /**
     * @var text
     */
    private $header;

    /**
     * @var string
     */
    private $content;

    /**
     * @var string
     */
    private $text;

    /**
     * @var string
     */
    private $footer;

    /**
     * @var string
     */
    private $meta_title;

    /**
     * @var text
     */
    private $meta_description;


    /**
     * Set title
     *
     * @param string $title
     * @return PageTranslation
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    public function setSubtitle($subtitle)
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    public function getSubtitle()
    {
        return $this->subtitle;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return TagTranslation
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    public function setHeader($header)
    {
        $this->header = $header;

        return $this;
    }

    public function getHeader()
    {
        return $this->header;
    }

    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    public function getText()
    {
        return $this->text;
    }

    public function setFooter($footer)
    {
        $this->footer = $footer;

        return $this;
    }

    public function getFooter()
    {
        return $this->footer;
    }

    /**
     * Set title
     */
    public function setMetaTitle($meta_title)
    {
        $this->meta_title = $meta_title;

        return $this;
    }

    /**
     * Get title
     */
    public function getMetaTitle()
    {
        return $this->meta_title;
    }

    /**
     * Set description
     */
    public function setMetaDescription($meta_description)
    {
        $this->meta_description = $meta_description;

        return $this;
    }

    /**
     * Get description
     */
    public function getMetaDescription()
    {
        return $this->meta_description;
    }

    /**
     * __toString()
     */
    public function __toString()
    {
        return $this->getTitle();
    }
}
