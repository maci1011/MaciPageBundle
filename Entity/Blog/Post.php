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
	 * @var \Maci\UserBundle\Entity\User
	 */
	private $author;

	/**
	 * @var \Doctrine\Common\Collections\Collection
	 */
	private $tags;

	/**
	 * @var \Doctrine\Common\Collections\Collection
	 */
	private $mediaItems;

	/**
	 * @var \Maci\PageBundle\Entity\Media
	 */
	private $preview;

	/**
	 * @var \Doctrine\Common\Collections\Collection
	 */
	private $relatedSources;

	/**
	 * @var \Doctrine\Common\Collections\Collection
	 */
	private $relatedTargets;

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
		$this->mediaItems = new \Doctrine\Common\Collections\ArrayCollection();
		$this->relatedSources = new \Doctrine\Common\Collections\ArrayCollection();
		$this->relatedTargets = new \Doctrine\Common\Collections\ArrayCollection();
		$this->removed = false;
		$this->status = 'new';
        $this->path = uniqid();
		$this->link = $uniqid;
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
	 * Set link
	 *
	 * @param string $link
	 * @return Page
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
	 * @param \Maci\UserBundle\Entity\User $author
	 * @return Post
	 */
	public function setAuthor(\Maci\UserBundle\Entity\User $author = null)
	{
		$this->author = $author;

		return $this;
	}

	/**
	 * Get author
	 *
	 * @return \Maci\UserBundle\Entity\User 
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
	 * Add relatedSource.
	 *
	 * @param \Maci\PageBundle\Entity\Blog\RelatedPosts $relatedSource
	 *
	 * @return Post
	 */
	public function addRelatedSource(\Maci\PageBundle\Entity\Blog\RelatedPosts $relatedSource)
	{
		$this->relatedSources[] = $relatedSource;

		return $this;
	}

	/**
	 * Remove relatedSource.
	 *
	 * @param \Maci\PageBundle\Entity\Blog\RelatedPosts $relatedSource
	 *
	 * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
	 */
	public function removeRelatedSource(\Maci\PageBundle\Entity\Blog\RelatedPosts $relatedSource)
	{
		return $this->relatedSources->removeElement($relatedSource);
	}

	/**
	 * Get relatedSources.
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getRelatedSources()
	{
		return $this->relatedSources;
	}

	/**
	 * Add relatedTarget.
	 *
	 * @param \Maci\PageBundle\Entity\Blog\RelatedPosts $relatedTarget
	 *
	 * @return Post
	 */
	public function addRelatedTarget(\Maci\PageBundle\Entity\Blog\RelatedPosts $relatedTarget)
	{
		$this->relatedTargets[] = $relatedTarget;

		return $this;
	}

	/**
	 * Remove relatedTarget.
	 *
	 * @param \Maci\PageBundle\Entity\Blog\RelatedPosts $relatedTarget
	 *
	 * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
	 */
	public function removeRelatedTarget(\Maci\PageBundle\Entity\Blog\RelatedPosts $relatedTarget)
	{
		return $this->relatedTargets->removeElement($relatedTarget);
	}

	/**
	 * Get relatedTargets.
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getRelatedTargets()
	{
		return $this->relatedTargets;
	}



	public function getPreviousSourcePosts()
	{
		return $this->relatedSources->filter(function($e){
			return ( is_object($e->getTargetPost()) && $e->getType() == 'prev' );
		});
	}

	public function getNextSourcePosts()
	{
		return $this->relatedSources->filter(function($e){
			return ( is_object($e->getTargetPost()) && $e->getType() == 'next' );
		});
	}

	public function getRelatedSourcePosts()
	{
		return $this->relatedSources->filter(function($e){
			return ( is_object($e->getTargetPost()) && $e->getType() == 'related' );
		});
	}



	public function getPreviousTargetPosts()
	{
		return $this->relatedTargets->filter(function($e){
			return ( is_object($e->getSourcePost()) && $e->getType() == 'prev' );
		});
	}

	public function getNextTargetPosts()
	{
		return $this->relatedTargets->filter(function($e){
			return ( is_object($e->getSourcePost()) && $e->getType() == 'next' );
		});
	}

	public function getRelatedTargetPosts()
	{
		return $this->relatedTargets->filter(function($e){
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

	public function getRelatedPosts()
	{
		return new \Doctrine\Common\Collections\ArrayCollection(
			array_merge($this->getRelatedSourcePosts()->toArray(), $this->getRelatedTargetPosts()->toArray())
		);
	}



	/**
	 * Add mediaItems
	 *
	 * @param \Maci\PageBundle\Entity\Blog\MediaItem $mediaItems
	 * @return Post
	 */
	public function addMediaItem(\Maci\PageBundle\Entity\Blog\MediaItem $mediaItems)
	{
		$this->mediaItems[] = $mediaItems;

		return $this;
	}

	/**
	 * Remove mediaItems
	 *
	 * @param \Maci\PageBundle\Entity\Blog\MediaItem $mediaItems
	 */
	public function removeMediaItem(\Maci\PageBundle\Entity\Blog\MediaItem $mediaItems)
	{
		$this->mediaItems->removeElement($mediaItems);
	}

	/**
	 * Get mediaItems
	 *
	 * @return \Doctrine\Common\Collections\Collection 
	 */
	public function getMediaItems()
	{
		return $this->mediaItems;
	}

	/**
	 * Get public mediaItems
	 *
	 * @return \Doctrine\Common\Collections\Collection 
	 */
	public function getPublicMedia()
	{
		return $this->getMediaItems()->filter(function($e){
			return $e->getMedia()->getPublic();
		});
	}

	/**
	 * Get mediaItems | Images
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
