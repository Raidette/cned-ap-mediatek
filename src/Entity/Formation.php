<?php

namespace App\Entity;

use App\Repository\FormationRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass=FormationRepository::class)
 */
class Formation
{

    /**
     * 
     * @var String
     */
    private const CHEMIN_IMAGE = "https://i.ytimg.com/vi/";
    
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\LessThan("tomorrow")
     */
    private $publishedAt;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $videoId;

    /**
     * @ORM\ManyToOne(targetEntity=Playlist::class, inversedBy="formations")
     */
    private $playlist;

    /**
     * @ORM\ManyToMany(targetEntity=Categorie::class, inversedBy="formations")
     */
    private $categories;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
    }
    
    /**
     * getId
     *
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }
    
    /**
     * getPublishedAt
     *
     * @return DateTimeInterface
     */
    public function getPublishedAt(): ?DateTimeInterface
    {
        return $this->publishedAt;
    }
    
    /**
     * setPublishedAt
     *
     * @param  mixed $publishedAt
     * @return self
     */
    public function setPublishedAt(?DateTimeInterface $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }
        
    /**
     * getPublishedAtString
     *
     * @return string
     */
    public function getPublishedAtString(): string {
        if($this->publishedAt == null){
            return "";
        }
        return $this->publishedAt->format('d/m/Y');     
    }      
    
    /**
     * getTitle
     *
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }
    
    /**
     * setTitle
     *
     * @param  mixed $title
     * @return self
     */
    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }
    
    /**
     * getDescription
     *
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }
    
    /**
     * setDescription
     *
     * @param  mixed $description
     * @return self
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }
    
    /**
     * getMiniature
     *
     * @return string
     */
    public function getMiniature(): ?string
    {
        return self::CHEMIN_IMAGE.$this->videoId."/default.jpg";
    }
    
    /**
     * getPicture
     *
     * @return string
     */
    public function getPicture(): ?string
    {
        return self::CHEMIN_IMAGE.$this->videoId."/hqdefault.jpg";
    }
    
    /**
     * getVideoId
     *
     * @return string
     */
    public function getVideoId(): ?string
    {
        return $this->videoId;
    }
    
    /**
     * setVideoId
     *
     * @param  mixed $videoId
     * @return self
     */
    public function setVideoId(?string $videoId): self
    {
        $this->videoId = $videoId;

        return $this;
    }
    
    /**
     * getPlaylist
     *
     * @return Playlist
     */
    public function getPlaylist(): ?Playlist
    {
        return $this->playlist;
    }
    
    /**
     * setPlaylist
     *
     * @param  mixed $playlist
     * @return self
     */
    public function setPlaylist(?Playlist $playlist): self
    {
        $this->playlist = $playlist;

        return $this;
    }

    /**
     * getCategories
     *
     * @return Collection
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }
    
    /**
     * addCategory
     *
     * @param  mixed $category
     * @return self
     */
    public function addCategory(Categorie $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
        }

        return $this;
    }
    
    /**
     * removeCategory
     *
     * @param  mixed $category
     * @return self
     */
    public function removeCategory(Categorie $category): self
    {
        $this->categories->removeElement($category);

        return $this;
    }
}
