<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ImageRepository")
 */
class Image
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User",inversedBy="image", cascade={"persist", "merge"})
     * @ORM\JoinColumn(name="user_image",referencedColumnName="id")
     */
    private $imageFile;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(name="imageSize", type="bigint")
     */
    private $imageSize;

    /**
     * @ORM\Column(name="imageExtension", type="string", length=255)
     */
    private $imageExtension;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImageFile(): ?User
    {
        return $this->imageFile;
    }

    public function setImageFile(User $imageFile): self
    {
        $this->imageFile = $imageFile;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getImageSize()
    {
        return $this->imageSize;
    }

    /**
     * @param mixed $imageSize
     */
    public function setImageSize($imageSize): void
    {
        $this->imageSize = $imageSize;
    }

    /**
     * @return mixed
     */
    public function getImageExtension()
    {
        return $this->imageExtension;
    }

    /**
     * @param mixed $imageExtension
     */
    public function setImageExtension($imageExtension): void
    {
        $this->imageExtension = $imageExtension;
    }
}
