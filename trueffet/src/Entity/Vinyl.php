<?php

namespace App\Entity;

use App\Repository\VinylRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VinylRepository::class)]
class Vinyl
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $nameAlbum = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column]
    private ?int $price = null;

    /**
     * @return string|null
     */
    public function getNameAlbum(): ?string
    {
        return $this->nameAlbum;
    }

    /**
     * @param string|null $nameAlbum
     */
    public function setNameAlbum(?string $nameAlbum): void
    {
        $this->nameAlbum = $nameAlbum;
    }

    #[ORM\Column(type: Types::SIMPLE_ARRAY)]
    private array $songList = [];

    #[ORM\Column]
    private ?bool $isCollector = null;

    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'vinyls', fetch: "EAGER")]
    private Collection $categories;

    #[ORM\Column(length: 255)]
    private ?string $album = null;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'vinyls')]
    private Collection $users;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getSongList(): array
    {
        return $this->songList;
    }

    public function setSongList(array $songList): static
    {
        $this->songList = $songList;

        return $this;
    }

    public function isIsCollector(): ?bool
    {
        return $this->isCollector;
    }

    public function setIsCollector(bool $isCollector): static
    {
        $this->isCollector = $isCollector;

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    public function removeCategory(Category $category): static
    {
        $this->categories->removeElement($category);

        return $this;
    }

    public function getAlbum(): ?string
    {
        return $this->album;
    }

    public function setAlbum(string $album): static
    {
        $this->album = $album;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addVinyl($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            $user->removeVinyl($this);
        }

        return $this;
    }


}
