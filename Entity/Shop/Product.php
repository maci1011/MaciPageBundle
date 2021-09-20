<?php

namespace Maci\PageBundle\Entity\Shop;

use Maci\PageBundle\Entity\Media\Media;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Product
 */
class Product
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
    private $composition;

    /**
     * @var string
     */
    private $meta_title;

    /**
     * @var text
     */
    private $meta_description;

    /**
     * @var string
     */
    private $locale;

    /**
     * @var decimal
     */
    private $price;

    /**
     * @var decimal
     */
    private $sale;

    /**
     * @var string
     */
    private $type;

    /**
     * @var boolean
     */
    private $shipment;

    /**
     * @var boolean
     */
    private $limited;

    /**
     * @var string
     */
    private $quantity;

    /**
     * @var string
     */
    private $status;

    /**
     * @var string
     */
    private $code;

    /**
     * @var boolean
     */
    private $tabbed;

    /**
     * @var string
     */
    private $variant_label;

    /**
     * @var string
     */
    private $variant_name;

    /**
     * @var integer
     */
    private $position;

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
     * @var \Maci\PageBundle\Entity\Shop\Category
     */
    private $parent;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $children;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $categoryItems;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $mediaItems;

    /**
     * @var \Maci\PageBundle\Entity\Media\Media
     */
    private $preview;

    /**
     * @var \Maci\PageBundle\Entity\Media\Media
     */
    private $cover;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $translations;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
        $this->categoryItems = new \Doctrine\Common\Collections\ArrayCollection();
        $this->mediaItems = new \Doctrine\Common\Collections\ArrayCollection();
        $this->translations = new \Doctrine\Common\Collections\ArrayCollection();
        $this->code = uniqid();
        $this->shipment = true;
        $this->limited = true;
        $this->quantity = 1;
        $this->status = array_keys($this->getStatusArray())[0];
        $this->tabbed = true;
        $this->removed = false;
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
     * @return Album
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
     * @return Album
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

    public function setComposition($composition)
    {
        $this->composition = $composition;

        return $this;
    }

    public function getComposition()
    {
        return $this->composition;
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
     * Set price
     *
     * @param string $price
     * @return Product
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string 
     */
    public function getPrice()
    {
        return $this->price;
    }

    public function setSale($sale)
    {
        $this->sale = $sale;

        return $this;
    }

    public function getSale()
    {
        return $this->sale;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Product
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

    public function setShipment($shipment)
    {
        $this->shipment = $shipment;

        return $this;
    }

    public function getShipment()
    {
        return $this->shipment;
    }

    /**
     * Set limited
     *
     * @param boolean $limited
     * @return Product
     */
    public function setLimited($limited)
    {
        $this->limited = $limited;

        return $this;
    }

    /**
     * Get limited
     *
     * @return boolean 
     */
    public function getLimited()
    {
        return $this->limited;
    }

    /**
     * Set quantity
     *
     * @param string $quantity
     * @return Product
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return string 
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    public function checkQuantity($quantity = 0)
    {
        if ($this->limited) {
            if ( ( $this->quantity - $quantity ) < 0 ) {
                return false;
            }
        }
        return true;
    }

    public function subQuantity($quantity)
    {
        if ($this->limited) {
            $this->quantity -= $quantity;
        }
    }

    public function addQuantity($quantity)
    {
        if ($this->limited) {
            $this->quantity += $quantity;
        }
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Product
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }

    public function getStatusArray()
    {
        return array(
            'new' => 'New Product',
            'available' => 'Available',
            'on_sale' => 'On Sale',
            'not_available' => 'Not Available',
            'foo' => 'Foo'
        );
    }

    public function getStatusLabel()
    {
        $array = $this->getStatusArray();
        if (array_key_exists($this->status, $array)) {
            return $array[$this->status];
        }
        $str = str_replace('_', ' ', $this->status);
        return ucwords($str);
    }

    /**
     * Set code
     *
     * @param string $code
     * @return Product
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    public function setTabbed($tabbed)
    {
        $this->tabbed = $tabbed;

        return $this;
    }

    public function getTabbed()
    {
        return $this->tabbed;
    }

    public function setVariantName($variant_name)
    {
        $this->variant_name = $variant_name;

        return $this;
    }

    public function getVariantName()
    {
        return $this->variant_name;
    }

    public function setVariantLabel($variant_label)
    {
        $this->variant_label = $variant_label;

        return $this;
    }

    public function getVariantLabel()
    {
        return $this->variant_label;
    }

    /**
     * Set position
     *
     * @param integer $position
     * @return Product
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return integer 
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Product
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
     * @return Product
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

    public function addChild(\Maci\PageBundle\Entity\Shop\Product $child)
    {
        $this->children[] = $child;

        return $this;
    }

    public function removeChild(\Maci\PageBundle\Entity\Shop\Product $child)
    {
        $this->children->removeElement($child);
    }

    public function getChildren()
    {
        return $this->children;
    }

    public function getCurrentChildren()
    {
        return $this->getChildren()->filter(function($e){
            return !$e->getRemoved();
        });
    }

    public function getTabbedChildren()
    {
        return $this->getCurrentChildren()->filter(function($e){
            return ( $e->getTabbed() && $e->isAvailable() );
        });
    }

    public function getTabbedAndNotAvailableChildren()
    {
        return $this->getCurrentChildren()->filter(function($e){
            return ( $e->getTabbed() && !$e->isAvailable() );
        });
    }

    public function getNotTabbedChildren()
    {
        return $this->getCurrentChildren()->filter(function($e){
            return ( !$e->getTabbed() && $e->isAvailable() );
        });
    }

    public function getNotTabbedAndNotAvailableChildren()
    {
        return $this->getCurrentChildren()->filter(function($e){
            return ( !$e->getTabbed() && !$e->isAvailable() );
        });
    }

    public function setParent(\Maci\PageBundle\Entity\Shop\Product $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

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

    public function getHierarchy()
    {
        $list = $this->getParentsList();
        $list[] = $this;
        return $list;
    }

    /**
     * Add categoryItems
     *
     * @param \Maci\PageBundle\Entity\Shop\CategoryItem $categoryItems
     * @return Product
     */
    public function addCategoryItem(\Maci\PageBundle\Entity\Shop\CategoryItem $categoryItems)
    {
        $this->categoryItems[] = $categoryItems;

        return $this;
    }

    /**
     * Remove categoryItems
     *
     * @param \Maci\PageBundle\Entity\Shop\CategoryItem $categoryItems
     */
    public function removeCategoryItem(\Maci\PageBundle\Entity\Shop\CategoryItem $categoryItems)
    {
        $this->categoryItems->removeElement($categoryItems);
    }

    /**
     * Get categoryItems
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCategoryItems()
    {
        return $this->categoryItems;
    }

    /**
     * Add mediaItems
     *
     * @param \Maci\PageBundle\Entity\Shop\MediaItem $mediaItems
     * @return Product
     */
    public function addMediaItem(\Maci\PageBundle\Entity\Shop\MediaItem $mediaItems)
    {
        $this->mediaItems[] = $mediaItems;

        if(is_null($this->preview)
            && 0 < $this->mediaItems->count()
            && is_object($this->mediaItems->get(0)->getMedia())
        ) {
            $this->preview = $this->mediaItems[0]->getMedia();
        }

        if(is_null($this->cover)
            && 1 < $this->mediaItems->count()
            && is_object($this->mediaItems->get(0)->getMedia())
        ) {
            $this->cover = $this->mediaItems[0]->getMedia();
        }

        return $this;
    }

    /**
     * Remove mediaItems
     *
     * @param \Maci\PageBundle\Entity\Shop\MediaItem $mediaItems
     */
    public function removeMediaItem(\Maci\PageBundle\Entity\Shop\MediaItem $mediaItems)
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

    public function getPrivateMedia()
    {
        return $this->getMediaItems()->filter(function($e){
            return !$e->getMedia()->getPublic();
        });
    }

    public function getPublicMedia()
    {
        return $this->getMediaItems()->filter(function($e){
            return $e->getMedia()->getPublic();
        });
    }

    public function getPrivateDocuments()
    {
        return $this->getPrivateMedia()->filter(function($e){
            return $e->getMedia()->getType() == 'document';
        });
    }

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
     * @return Product
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

    /**
     * Set cover
     *
     * @param \Maci\PageBundle\Entity\Media\Media $cover
     * @return Product
     */
    public function setCover(\Maci\PageBundle\Entity\Media\Media $cover = null)
    {
        $this->cover = $cover;

        return $this;
    }

    /**
     * Get cover
     *
     * @return \Maci\PageBundle\Entity\Media\Media 
     */
    public function getCover()
    {
        return $this->cover;
    }

    /**
     * setUpdatedValue
     */
    public function setUpdatedValue()
    {
        $this->created = new \DateTime();
    }

    /**
     * setCreatedValue
     */
    public function setCreatedValue()
    {
        $this->updated = new \DateTime();
    }

    public function isAvailable()
    {
        if ($this->removed) {
            return false;
        } elseif ($this->status === 'not_available') {
            return false;
        } elseif (!$this->price || $this->price <= 0) {
            return false;
        } elseif ($this->limited && $this->quantity < 1) {
            return false;
        } elseif (0 < count($this->getNotTabbedAndNotAvailableChildren()) && 0 == count($this->getNotTabbedChildren())) {
            return false;
        }
        return true;
    }

    public function isOnSale()
    {
        if (0 < $this->sale) return true;
        return false;
    }

    public function getAmount()
    {
        if (0 < $this->sale) return $this->sale;
        return $this->price;
    }

    public function isCoverInMediaList()
    {
        if (!is_object($this->cover)) {
            return false;
        }
        foreach ($this->mediaItems as $item) {
            if ($item->getId() === $this->cover->getId()) return true;
        }
        return false;
    }

    /**
    * Create a product from an image (used in the uploader)
    */
    public function setFile(UploadedFile $file = null)
    {
        if ($file === null) return;

        $preview = new Media();
        $preview->setFile($file);
        $this->setPreview($preview);

        $name = explode('-', explode('.', $file->getClientOriginalName())[0]);
        if (strlen($name[0])) $this->setName(trim($name[0]));
        if (1<count($name) && strlen($name[1])) $this->setCode(trim($name[1]));
        if (2<count($name) && strlen($name[2])) $this->setDescription(trim($name[2]));
    }

    /**
     * "Inherited" Methos
     */

    public function getInhName()
    {
        if (0 < strlen($this->name)) return $this->name;
        $name = false;
        $list = $this->getParentsList();
        foreach ($list as $item) {
            if (0 < strlen($item->getName())) $name = $item->getName();
        }
        return $name;
    }

    public function getInhDescription()
    {
        if (0 < strlen($this->description)) return $this->description;
        $description = false;
        $list = $this->getParentsList();
        foreach ($list as $item) {
            if (0 < strlen($item->getDescription())) $description = $item->getDescription();
        }
        return $description;
    }

    public function getInhPreview()
    {
        if (is_object($this->preview)) return $this->preview;
        $preview = null;
        $list = $this->getParentsList();
        foreach ($list as $item) {
            if (is_object($item->getPreview())) $preview = $item->getPreview();
        }
        return $preview;
    }

    /**
     * __toString()
     */
    public function __toString()
    {
        return ( 0 < strlen($this->getName()) ? $this->getName() : ('Product[' . ($this->id ? $this->id : 'tmp') . ']') );
    }
}
