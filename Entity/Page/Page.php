<?php

namespace Maci\PageBundle\Entity\Page;

/**
 * Page
 */
class Page
{
	/**
	 * @var integer
	 */
	private $id;

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
	 * @var string
	 */
	private $menu_title;

	/**
	 * @var text
	 */
	private $meta_description;

	/**
	 * @var integer
	 */
	private $status;

	/**
	 * @var string
	 */
	private $path;

	/**
	 * @var string
	 */
	private $redirect;

	/**
	 * @var string
	 */
	private $template;

	/**
	 * @var string
	 */
	private $map;

	/**
	 * @var string
	 */
	private $tag;

	/**
	 * @var \DateTime
	 */
	private $created;

	/**
	 * @var \DateTime
	 */
	private $updated;

	/**
	 * @var boolean
	 */
	private $removed;

	/**
	 * @var string
	 */
	private $album;

	/**
	 * @var string
	 */
	private $gallery;

	/**
	 * @var string
	 */
	private $slider;

	/**
	 * @var string
	 */
	private $category;

	/**
	 * @var \Maci\PageBundle\Entity\Page\Page
	 */
	private $parent;

	/**
	 * @var \Doctrine\Common\Collections\Collection
	 */
	protected $children;

	/**
	 * @var \Doctrine\Common\Collections\Collection
	 */
	private $slides;

	/**
	 * @var string
	 */
	private $locale;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->status = 'active';
		$this->removed = false;
		$this->path = ( 'page-' . uniqid() );
		$this->slides = new \Doctrine\Common\Collections\ArrayCollection();
		$this->children = new \Doctrine\Common\Collections\ArrayCollection();
	}

	/**
	 * Get id
	 *
	 * @return integer 
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Set title
	 *
	 * @param string $title
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
	 * Set menu_title
	 */
	public function setMenuTitle($menu_title)
	{
		$this->menu_title = $menu_title;

		return $this;
	}

	/**
	 * Get menu_title
	 */
	public function getMenuTitle()
	{
		return $this->menu_title;
	}

	/**
	 * Get menu_title
	 */
	public function getMenuLabel()
	{
		return ( strlen($this->menu_title) ? $this->menu_title : $this->title );
	}

	/**
	 * Set meta_title
	 */
	public function setMetaTitle($meta_title)
	{
		$this->meta_title = $meta_title;

		return $this;
	}

	/**
	 * Get meta_title
	 */
	public function getMetaTitle()
	{
		return $this->meta_title;
	}

	/**
	 * Set meta_description
	 */
	public function setMetaDescription($meta_description)
	{
		$this->meta_description = $meta_description;

		return $this;
	}

	/**
	 * Get meta_description
	 */
	public function getMetaDescription()
	{
		return $this->meta_description;
	}

	/**
	 * Set status
	 *
	 * @param integer $status
	 * @return Page
	 */
	public function setStatus($status)
	{
		$this->status = $status;

		return $this;
	}

	/**
	 * Get status
	 *
	 * @return integer 
	 */
	public function getStatus()
	{
		return $this->status;
	}

	/**
	 * Set path
	 *
	 * @param string $path
	 * @return Page
	 */
	public function setPath($path)
	{
		$this->path = $path;

		return $this;
	}

	/**
	 * Get path
	 *
	 * @return string 
	 */
	public function getPath()
	{
		return $this->path;
	}

	/**
	 * Set redirect
	 *
	 * @param string $redirect
	 * @return string
	 */
	public function setRedirect($redirect)
	{
		$this->redirect = $redirect;

		return $this;
	}

	/**
	 * Get redirect
	 *
	 * @return string 
	 */
	public function getRedirect()
	{
		return $this->redirect;
	}

	public function setTemplate($template)
	{
		$this->template = $template;

		return $this;
	}

	public function getTemplate()
	{
		return $this->template;
	}

	public function getTemplateArray()
	{
		return array(
			'Page' => '@MaciPage/Page/page.html.twig',
			'Full Page' => '@MaciPage/Page/fullpage.html.twig',
			'Slide Page' => '@MaciPage/Page/slidepage.html.twig',
			'Homepage' => '@MaciPage/Page/homepage.html.twig',
			'About' => '@MaciPage/Page/about.html.twig',
			'Contacts' => '@MaciPage/Page/contacts.html.twig',
			'Zero' => '@MaciPage/Page/template_zero.html.twig',
			'One' => '@MaciPage/Page/template_one.html.twig',
			'Two' => '@MaciPage/Page/template_two.html.twig',
			'Three' => '@MaciPage/Page/template_three.html.twig',
			'Foo' => '@MaciPage/Page/foo.html.twig'
		);
	}

	public function getTemplateLabel()
	{
		$array = $this->getTemplateArray();
		$key = array_search($this->template, $array);
		if ($key) {
			return $key;
		}
		$str = str_replace('_', ' ', $this->template);
		return ucwords($str);
	}

	/**
	 * Set map
	 *
	 * @param string $map
	 * @return Page
	 */
	public function setMap($map)
	{
		$this->map = $map;

		return $this;
	}

	/**
	 * Get map
	 *
	 * @return string 
	 */
	public function getMap()
	{
		return $this->map;
	}

	/**
	 * Set album
	 *
	 * @param string $album
	 * @return Page
	 */
	public function setAlbum($album)
	{
		$this->album = $album;

		return $this;
	}

	/**
	 * Get album
	 *
	 * @return string 
	 */
	public function getAlbum()
	{
		return $this->album;
	}

	/**
	 * Set gallery
	 *
	 * @param string $gallery
	 * @return Page
	 */
	public function setGallery($gallery)
	{
		$this->gallery = $gallery;

		return $this;
	}

	/**
	 * Get gallery
	 *
	 * @return string 
	 */
	public function getGallery()
	{
		return $this->gallery;
	}

	/**
	 * Set slider
	 *
	 * @param string $slider
	 * @return Page
	 */
	public function setSlider($slider)
	{
		$this->slider = $slider;

		return $this;
	}

	/**
	 * Get slider
	 *
	 * @return string 
	 */
	public function getSlider()
	{
		return $this->slider;
	}

	/**
	 * Add slides
	 *
	 * @param \Maci\PageBundle\Entity\Page\Slide $slides
	 * @return Page
	 */
	public function addSlide(\Maci\PageBundle\Entity\Page\Slide $slides)
	{
		$this->slides[] = $slides;

		return $this;
	}

	/**
	 * Remove slides
	 *
	 * @param \Maci\PageBundle\Entity\Page\Slide $slides
	 */
	public function removeSlide(\Maci\PageBundle\Entity\Page\Slide $slides)
	{
		$this->slides->removeElement($slides);
	}

	/**
	 * Get slides
	 *
	 * @return \Doctrine\Common\Collections\Collection 
	 */
	public function getSlides()
	{
		return $this->slides;
	}

	public function setCategory($category)
	{
		$this->category = $category;

		return $this;
	}

	public function getCategory()
	{
		return $this->category;
	}

	public function setTag($tag)
	{
		$this->tag = $tag;

		return $this;
	}

	public function getTag()
	{
		return $this->tag;
	}

	/**
	 * Set created
	 *
	 * @param \DateTime $created
	 * @return Page
	 */
	public function setCreated($created)
	{
		$this->created = $created;

		return $this;
	}

	/**
	 * Get created
	 *
	 * @return \DateTime 
	 */
	public function getCreated()
	{
		return $this->created;
	}

	/**
	 * Set updated
	 *
	 * @param \DateTime $updated
	 * @return Page
	 */
	public function setUpdated($updated)
	{
		$this->updated = $updated;

		return $this;
	}

	/**
	 * Get updated
	 *
	 * @return \DateTime 
	 */
	public function getUpdated()
	{
		return $this->updated;
	}

	public function setRemoved($removed)
	{
		$this->removed = $removed;

		return $this;
	}

	public function getRemoved()
	{
		return $this->removed;
	}

	/**
	 * Set parent
	 *
	 * @param \Maci\PageBundle\Entity\Page\Page $parent
	 * @return Page
	 */
	public function setParent(\Maci\PageBundle\Entity\Page\Page $parent = null)
	{
		$this->parent = $parent;

		return $this;
	}

	/**
	 * Get parent
	 *
	 * @return \Maci\PageBundle\Entity\Page\Page 
	 */
	public function getParent()
	{
		return $this->parent;
	}

	public function getParentsList()
	{
		$list = array();
		$obj = $this->parent;
		while (is_object($obj)) {
			$list[] = $obj;
			$obj = $obj->getParent();
		}
		return array_reverse($list);
	}

	/**
	 * Add children
	 *
	 * @param \Maci\PageBundle\Entity\Page\Page $children
	 * @return Page
	 */
	public function addChild(\Maci\PageBundle\Entity\Page\Page $children)
	{
		$this->children[] = $children;

		return $this;
	}

	/**
	 * Remove children
	 *
	 * @param \Maci\PageBundle\Entity\Page\Page $children
	 */
	public function removeChild(\Maci\PageBundle\Entity\Page\Page $children)
	{
		$this->children->removeElement($children);
	}

	/**
	 * Get children
	 *
	 * @return \Doctrine\Common\Collections\Collection 
	 */
	public function getChildren()
	{
		return $this->children;
	}

	public function getCurrentChildren()
	{
		return $this->children->filter(function($e){
			return !$e->getRemoved();
		});
	}

	public function hasCurrentChildren()
	{
		return !!count($this->getCurrentChildren());
	}

	/**
	 * setUpdatedValue
	 */
	public function setUpdatedValue()
	{
		$this->updated = new \DateTime();
	}

	/**
	 * setCreatedValue
	 */
	public function setCreatedValue()
	{
		$this->created = new \DateTime();
	}

	public function setLocale($locale)
	{
		$this->locale = $locale;

		return $this;
	}

	public function getLocale()
	{
		return $this->locale;
	}

	/**
	 * __toString()
	 */
	public function __toString()
	{
		return strlen($this->getTitle()) ? $this->getTitle() : ('PageItem_' . ($this->id ? $this->id : 'new'));
	}
}
