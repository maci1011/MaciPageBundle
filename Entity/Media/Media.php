<?php

namespace Maci\PageBundle\Entity\Media;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Media
 */
class Media
{
	/**
	 * @var integer
	 */
	private $id;

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var string
	 */
	private $description;

	/**
	 * @var string
	 */
	private $original;

	/**
	 * @var string
	 */
	private $path;

	/**
	 * Unmapped property to handle file uploads
	 * @var string
	 */
	private $file;

	/**
	 * @var boolean
	 */
	private $public;

	/**
	 * @var string
	 */
	private $mimetype;

	/**
	 * @var string
	 */
	private $type;

	/**
	 * @var string
	 */
	private $token;

	/**
	 * @var \DateTime
	 */
	private $created;

	/**
	 * @var \DateTime
	 */
	private $updated;

	/**
	 * @var \Doctrine\Common\Collections\Collection
	 */
	private $tags;

	/**
	 * @var \Maci\UserBundle\Entity\User
	 */
	private $user;

	/**
	 * @var \Doctrine\Common\Collections\Collection
	 */
	protected $permissions;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->name = null;
		$this->public = true;
		$this->type = 'media';
		$this->tags = new \Doctrine\Common\Collections\ArrayCollection();
		$this->permissions = new \Doctrine\Common\Collections\ArrayCollection();
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
	 * Set name
	 *
	 * @param string $name
	 * @return MediaTranslation
	 */
	public function setName($name)
	{
		$this->name = $name;

		return $this;
	}

	/**
	 * Get name
	 *
	 * @return string 
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Set description
	 *
	 * @param string $description
	 * @return MediaTranslation
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

	/**
	 * Set original
	 *
	 * @param string $original
	 * @return Media
	 */
	public function setOriginal($original)
	{
		$this->original = $original;

		return $this;
	}

	/**
	 * Get original
	 *
	 * @return string 
	 */
	public function getOriginal()
	{
		return $this->original;
	}

	/**
	 * Set path
	 *
	 * @param string $path
	 * @return Media
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
	 * Get web path
	 *
	 * @return string 
	 */
	public function getWebPath()
	{
		return ( '/' . $this->getUploadDir() . $this->path );
	}

	/**
	 * Get type path
	 *
	 * @return string
	 */
	public function getUploadDir()
	{
		return 'uploads/media_bundle/media/';
	}

	/**
	 * Get type path
	 *
	 * @return string
	 */
	public function getPublicDir()
	{
		return __DIR__.'/../../../../../../../public/';
	}

	/**
	 * Get media path
	 *
	 * @return string 
	 */
	public function getUploadRootDir()
	{
		return ( $this->getPublicDir() . $this->getUploadDir() );
	}

	/**
	 * Get path
	 *
	 * @return string 
	 */
	public function getAbsolutePath()
	{
		return ( $this->getUploadRootDir() . $this->path );
	}

	public function getWebPreview()
	{
		if ($this->type === 'image') {
			return $this->getWebPath();
		} elseif ($this->type === 'document') {
			return '/images/defaults/document-icon.png';
		} else {
			return '/images/defaults/no-icon.png';
		}
		return $this->getWebPath();
	}

	/**
	* Sets file.
	*
	* @param UploadedFile $file
	*/
	public function setFile(UploadedFile $file = null)
	{
		$this->file = $file;

		// check if we have an old image path
		if (isset($this->path)) {
			// store the old name to delete after the update
			$this->temp = $this->path;
			$this->path = null;
		}
	}

	/**
	* Get file.
	*
	* @return UploadedFile
	*/
	public function getFile()
	{
		return $this->file;
	}

	/**
	 * Set created
	 *
	 * @param \DateTime $created
	 * @return Media
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
	 * @return Media
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
	 * Set public
	 *
	 * @param boolean $public
	 * @return Media
	 */
	public function setPublic($public)
	{
		$this->public = $public;

		return $this;
	}

	/**
	 * Get public
	 *
	 * @return boolean 
	 */
	public function getPublic()
	{
		return $this->public;
	}

	/**
	 * Set token
	 *
	 * @param string $token
	 * @return Order
	 */
	public function setToken($token)
	{
		$this->token = $token;

		return $this;
	}

	public function setNewToken()
	{
		$this->token = sha1(uniqid(mt_rand(), true));
	}

	/**
	 * Get token
	 *
	 * @return string 
	 */
	public function getToken()
	{
		return $this->token;
	}

	/**
	 * Set mimetype
	 *
	 * @param string $mimetype
	 * @return Order
	 */
	public function setMimetype($mimetype)
	{
		$this->mimetype = $mimetype;

		return $this;
	}

	/**
	 * Get mimetype
	 *
	 * @return string 
	 */
	public function getMimetype()
	{
		return $this->mimetype;
	}

	/**
	 * Set type
	 *
	 * @param string $type
	 * @return Media
	 */
	public function setType($type)
	{
		$this->type = $type;

		return $this;
	}

	/**
	 * Get type
	 *
	 * @return string 
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * Get Type Array
	 */
	public function getTypeArray()
	{
		return array(
			'Document' => 'document',
			'Image' => 'image',
			'Media' => 'media'
		);
	}

	public function getTypeLabel()
	{
		$array = $this->getTypeArray();
		$key = array_search($this->type, $array);
		if ($key) {
			return $key;
		}
		$str = str_replace('_', ' ', $this->type);
		return ucwords($str);
	}

	/**
	 * Manages the copying of the file to the relevant place on the server
	 */
	public function upload()
	{
		// the file property can be empty if the field is not required
		if (null === $this->getFile()) {
			return;
		}

		$this->original = $this->getFile()->getClientOriginalName();

		$this->setNewToken();

		$this->path = md5($this->token) . '.' . $this->getFile()->getClientOriginalExtension();

		$this->mimetype = $this->getFile()->getClientMimeType();

		$smime = explode('/', $this->mimetype);
		if ($smime[0] === 'image') {
			$this->type = 'image';
		} else if (in_array($this->mimetype, array('application/pdf', 'text/plain', 'application/epub+zip', 'application/msword'))) {
			$this->type = 'document';
		} else {
			$this->type = 'media';
		}

		// move takes the target type and target filename as params
		$this->getFile()->move(
			$this->getUploadRootDir(),
			$this->path
		);

		if ($this->name === null) {
			$name = explode('.', $this->original)[0];
			if (0 < strpos($name, '-')) {
				$this->description = trim(str_replace('_', ' ', substr($name, (strpos($name, '-') + 1), strlen($name))));
				$this->name = trim(str_replace('_', ' ', substr($name, 0, strpos($name, '-'))));
			} else
				$this->name = $name;
		}

		// check if we have an old image
		if (isset($this->temp)) {
			// delete the old image
			unlink($this->getUploadRootDir() . $this->temp);
			// clear the temp image path
			$this->temp = null;
		}

		// clean up the file property as you won't need it anymore
		$this->file = null;
	}

	public function removeUpload()
	{
		if (strlen($this->path) && file_exists($this->getAbsolutePath())) {
			unlink($this->getAbsolutePath());
			$this->path = null;
		}
	}

	/**
	 * Lifecycle callback to upload the file to the server
	 */
	public function fileUpload() {
		$this->upload();
	}

	/**
	 * Add tags
	 *
	 * @param \Maci\PageBundle\Entity\Media\Tag $tags
	 * @return Post
	 */
	public function addTag(\Maci\PageBundle\Entity\Media\Tag $tags)
	{
		$this->tags[] = $tags;

		return $this;
	}

	/**
	 * Remove tags
	 *
	 * @param \Maci\PageBundle\Entity\Media\Tag $tags
	 */
	public function removeTag(\Maci\PageBundle\Entity\Media\Tag $tags)
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
	 * Set user
	 *
	 * @param \Maci\UserBundle\Entity\User $user
	 * @return Media
	 */
	public function setUser(\Maci\UserBundle\Entity\User $user = null)
	{
		$this->user = $user;

		return $this;
	}

	/**
	 * Get user
	 *
	 * @return \Maci\UserBundle\Entity\User 
	 */
	public function getUser()
	{
		return $this->user;
	}

	/**
	 * Add permissions
	 *
	 * @param \Maci\PageBundle\Entity\Media\Permission $permission
	 * @return Media
	 */
	public function addPermission(\Maci\PageBundle\Entity\Media\Permission $permission)
	{
		$this->permissions[] = $permission;

		return $this;
	}

	/**
	 * Remove permissions
	 *
	 * @param \Maci\PageBundle\Entity\Media\Permission $permission
	 */
	public function removePermission(\Maci\PageBundle\Entity\Media\Permission $permission)
	{
		$this->permissions->removeElement($permission);
	}

	/**
	 * Get permissions
	 *
	 * @return \Doctrine\Common\Collections\Collection 
	 */
	public function getPermissions()
	{
		return $this->permissions;
	}

	/**
	 * 
	 */
	public function setUpdatedValue()
	{
		$this->updated = new \DateTime;
	}

	/**
	 * 
	 */
	public function setCreatedValue()
	{
		$this->created = new \DateTime;
	}

	/**
	 * Get media size
	 *
	 * @return string
	 */
	public function getSize()
	{
		if(!$this->path) {
			return false;
		}
		return filesize($this->getAbsolutePath());
	}

	/**
	 * Get media size label
	 *
	 * @return string
	 */
	public function getSizeLabel()
	{
		$size = $this->getSize();
		if(!$size) {
			return 'undefined';
		}
		$index = 0;
		while (1024 < $size) {
			$size = $size / 1024;
			$index++;
		}
		$units = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
		if(4 < $index) {
			$index = 4;
		}
		return (number_format($size, 1) . ' ' . $units[$index]);
	}

	/**
	 * __toString()
	 */
	public function __toString()
	{
		return (is_string($this->getName()) ? $this->getName() : '');
	}
}
