<?php

namespace Maci\PageBundle\Entity\Blog;

/**
 * Post
 */
class Post
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
	 * @var string
	 */
	private $content;

	/**
	 * @var string
	 */
	private $markdown;

	/**
	 * @var string
	 */
	private $preheader;

	/**
	 * @var string
	 */
	private $header;

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
	private $link;

	/**
	 * @var string
	 */
	private $permalink;

	/**
	 * @var string
	 */
	private $meta_title;

	/**
	 * @var text
	 */
	private $meta_description;

	/**
	 * @var \DateTime
	 */
	private $created;

	/**
	 * @var \DateTime
	 */
	private $updated;

	/**
	 * @var \DateTime
	 */
	private $pubblished;

	/**
	 * @var boolean
	 */
	private $removed;

	/**
	 * @var \Maci\PageBundle\Entity\Blog\Author
	 */
	private $author;

	/**
	 * @var \Doctrine\Common\Collections\Collection
	 */
	private $tags;

	/**
	 * @var \Doctrine\Common\Collections\Collection
	 */
	private $items;

	/**
	 * @var \Maci\PageBundle\Entity\Media\Media
	 */
	private $preview;

	/**
	 * @var \Doctrine\Common\Collections\Collection
	 */
	private $sources;

	/**
	 * @var \Doctrine\Common\Collections\Collection
	 */
	private $targets;

	/**
	 * @var string
	 */
	private $locale;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->tags = new \Doctrine\Common\Collections\ArrayCollection();
		$this->items = new \Doctrine\Common\Collections\ArrayCollection();
		$this->sources = new \Doctrine\Common\Collections\ArrayCollection();
		$this->targets = new \Doctrine\Common\Collections\ArrayCollection();
		$this->removed = false;
		$this->status = 'new';
        $this->path = uniqid();
		$this->link = $this->path;
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
	 * @return PostTranslation
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
	 * Set content
	 *
	 * @param string $content
	 * @return PostTranslation
	 */
	public function setContent($content)
	{
		$this->content = $content;

		return $this;
	}

	/**
	 * Get content
	 *
	 * @return string 
	 */
	public function getContent()
	{
		return $this->content;
	}

	/**
	 * Set markdown
	 *
	 * @param string $markdown
	 * @return PostTranslation
	 */
	public function setMarkdown($markdown)
	{
		$this->markdown = $markdown;

		return $this;
	}

	/**
	 * Get markdown
	 *
	 * @return string 
	 */
	public function getMarkdown()
	{
		return $this->markdown;
	}

	/**
	 * Set preheader
	 *
	 * @param string $preheader
	 * @return PostTranslation
	 */
	public function setPreheader($preheader)
	{
		$this->preheader = $preheader;

		return $this;
	}

	/**
	 * Get preheader
	 *
	 * @return string 
	 */
	public function getPreheader()
	{
		return $this->preheader;
	}

	/**
	 * Set header
	 *
	 * @param string $header
	 * @return PostTranslation
	 */
	public function setHeader($header)
	{
		$this->header = $header;

		return $this;
	}

	/**
	 * Get header
	 *
	 * @return string 
	 */
	public function getHeader()
	{
		return $this->header;
	}

	/**
	 * Set status
	 *
	 * @param integer $status
	 * @return Post
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
	 * Get Status Array
	 */
	public function getStatusArray()
	{
		return array(
			'New' => 'new',
			'Draft' => 'draft',
			// 'Scheduled' => 'scheduled',
			'Pubblished' => 'pubblished',
			'Updated' => 'updated'
		);
	}

	public function getStatusLabel()
	{
		$array = $this->getStatusArray();
		$key = array_search($this->status, $array);
		if ($key) {
			return $key;
		}
		$str = str_replace('_', ' ', $this->status);
		return ucwords($str);
	}

	public function isPubblished()
	{
		return ($this->status == 'pubblished' || $this->status == 'updated');
	}

	public function isUpdated()
	{
		return ($this->status == 'updated');
	}

	/**
	 * Set path
	 *
	 * @param string $path
	 * @return Post
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
	 * Set link
	 *
	 * @param string $link
	 * @return Post
	 */
	public function setLink($link)
	{
		$this->link = $link;

		return $this;
	}

	/**
	 * Get link
	 *
	 * @return string 
	 */
	public function getLink()
	{
		return $this->link;
	}

	/**
	 * Set permalink
	 *
	 * @param string $permalink
	 * @return Post
	 */
	public function setPermalink($permalink)
	{
		$this->permalink = $permalink;

		return $this;
	}

	/**
	 * Get permalink
	 *
	 * @return string 
	 */
	public function getPermalink()
	{
		return $this->permalink;
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
	 * Set created
	 *
	 * @param \DateTime $created
	 * @return Post
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
	 * @return Post
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

	/**
	 * Set pubblished
	 *
	 * @param \DateTime $pubblished
	 * @return Post
	 */
	public function setPubblished($pubblished)
	{
		$this->pubblished = $pubblished;

		return $this;
	}

	/**
	 * Get pubblished
	 *
	 * @return \DateTime 
	 */
	public function getPubblished()
	{
		return $this->pubblished;
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
	 * Set author
	 *
	 * @param \Maci\PageBundle\Entity\Blog\Author $author
	 * @return Post
	 */
	public function setAuthor(\Maci\PageBundle\Entity\Blog\Author $author = null)
	{
		$this->author = $author;

		return $this;
	}

	/**
	 * Get author
	 *
	 * @return \Maci\PageBundle\Entity\Blog\Author 
	 */
	public function getAuthor()
	{
		return $this->author;
	}

	/**
	 * Add tags
	 *
	 * @param \Maci\PageBundle\Entity\Blog\Tag $tags
	 * @return Post
	 */
	public function addTag(\Maci\PageBundle\Entity\Blog\Tag $tags)
	{
		$this->tags[] = $tags;

		return $this;
	}

	/**
	 * Remove tags
	 *
	 * @param \Maci\PageBundle\Entity\Blog\Tag $tags
	 */
	public function removeTag(\Maci\PageBundle\Entity\Blog\Tag $tags)
	{
		$this->tags->removeElement($tags);
	}

	/**
	 * Get tags
	 *
	 * @return \Doctrine\Common\Collections\Collection 
	 */
	public function getTags()
	{
		return $this->tags;
	}

	/**
	 * Add Source.
	 *
	 * @param \Maci\PageBundle\Entity\Blog\Related $source
	 *
	 * @return Post
	 */
	public function addSource(\Maci\PageBundle\Entity\Blog\Related $source)
	{
		$this->sources[] = $source;

		return $this;
	}

	/**
	 * Remove Source.
	 *
	 * @param \Maci\PageBundle\Entity\Blog\Related $source
	 *
	 * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
	 */
	public function removeSource(\Maci\PageBundle\Entity\Blog\Related $source)
	{
		return $this->sources->removeElement($source);
	}

	/**
	 * Get Sources.
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getSources()
	{
		return $this->sources;
	}

	/**
	 * Add Target.
	 *
	 * @param \Maci\PageBundle\Entity\Blog\Related $target
	 *
	 * @return Post
	 */
	public function addTarget(\Maci\PageBundle\Entity\Blog\Related $target)
	{
		$this->targets[] = $target;

		return $this;
	}

	/**
	 * Remove Target.
	 *
	 * @param \Maci\PageBundle\Entity\Blog\Related $target
	 *
	 * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
	 */
	public function removeTarget(\Maci\PageBundle\Entity\Blog\Related $target)
	{
		return $this->targets->removeElement($target);
	}

	/**
	 * Get Targets.
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getTargets()
	{
		return $this->targets;
	}



	public function getPreviousSourcePosts()
	{
		return $this->sources->filter(function($e){
			return ( is_object($e->getTargetPost()) && $e->getType() == 'prev' );
		});
	}

	public function getNextSourcePosts()
	{
		return $this->sources->filter(function($e){
			return ( is_object($e->getTargetPost()) && $e->getType() == 'next' );
		});
	}

	public function getRelatedSourcePosts()
	{
		return $this->sources->filter(function($e){
			return ( is_object($e->getTargetPost()) && $e->getType() == 'related' );
		});
	}



	public function getPreviousTargetPosts()
	{
		return $this->targets->filter(function($e){
			return ( is_object($e->getSourcePost()) && $e->getType() == 'prev' );
		});
	}

	public function getNextTargetPosts()
	{
		return $this->targets->filter(function($e){
			return ( is_object($e->getSourcePost()) && $e->getType() == 'next' );
		});
	}

	public function getRelatedTargetPosts()
	{
		return $this->targets->filter(function($e){
			return ( is_object($e->getSourcePost()) && $e->getType() == 'related' );
		});
	}



	public function getPreviousPosts()
	{
		return new \Doctrine\Common\Collections\ArrayCollection(
			array_merge($this->getPreviousSourcePosts()->toArray(), $this->getNextTargetPosts()->toArray())
		);
	}

	public function getNextPosts()
	{
		return new \Doctrine\Common\Collections\ArrayCollection(
			array_merge($this->getNextSourcePosts()->toArray(), $this->getPreviousTargetPosts()->toArray())
		);
	}

	public function getRelated()
	{
		return new \Doctrine\Common\Collections\ArrayCollection(
			array_merge($this->getRelatedSourcePosts()->toArray(), $this->getRelatedTargetPosts()->toArray())
		);
	}



	/**
	 * Add items
	 *
	 * @param \Maci\PageBundle\Entity\Blog\Item $items
	 * @return Post
	 */
	public function addItem(\Maci\PageBundle\Entity\Blog\Item $items)
	{
		$this->items[] = $items;

		return $this;
	}

	/**
	 * Remove items
	 *
	 * @param \Maci\PageBundle\Entity\Blog\Item $items
	 */
	public function removeItem(\Maci\PageBundle\Entity\Blog\Item $items)
	{
		$this->items->removeElement($items);
	}

	/**
	 * Get items
	 *
	 * @return \Doctrine\Common\Collections\Collection 
	 */
	public function getItems()
	{
		return $this->items;
	}

	/**
	 * Get public items
	 *
	 * @return \Doctrine\Common\Collections\Collection 
	 */
	public function getPublicMedia()
	{
		return $this->getItems()->filter(function($e){
			return $e->getMedia()->getPublic();
		});
	}

	/**
	 * Get items | Images
	 *
	 * @return \Doctrine\Common\Collections\Collection 
	 */
	public function getImages()
	{
		return $this->getPublicMedia()->filter(function($e){
			return $e->getMedia()->getType() == 'image';
		});
	}

	/**
	 * Get items | !Images
	 *
	 * @return \Doctrine\Common\Collections\Collection 
	 */
	public function getOtherMedias()
	{
		return $this->getPublicMedia()->filter(function($e){
			return $e->getMedia()->getType() != 'image';
		});
	}

	/**
	 * Set preview
	 *
	 * @param \Maci\PageBundle\Entity\Media\Media $preview
	 * @return Post
	 */
	public function setPreview(\Maci\PageBundle\Entity\Media\Media $preview = null)
	{
		$this->preview = $preview;

		return $this;
	}

	/**
	 * Get preview
	 *
	 * @return \Maci\PageBundle\Entity\Media\Media 
	 */
	public function getPreview()
	{
		return $this->preview;
	}

	public function getWebPreview()
	{
		if (!$this->preview) {
			return '/images/defaults/document-icon.png';
		}
		return $this->preview->getWebPath();
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
	 * setUpdatedValue
	 */
	public function setUpdatedValue()
	{
		if ($this->status == 'pubblished' && $this->pubblished == null) {
			$this->pubblished = new \DateTime();
		}
		$this->updated = new \DateTime();
	}

	/**
	 * setCreatedValue
	 */
	public function setCreatedValue()
	{
		if ($this->status == 'new') {
			$this->status = 'draft';
		}
		$this->created = new \DateTime();
	}

	/**
	 * __toString()
	 */
	public function __toString()
	{
		return $this->getTitle();
	}
}
