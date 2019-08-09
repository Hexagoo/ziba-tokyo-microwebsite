<?php

namespace App\Entity;

use Sonata\MediaBundle\Model\MediaInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProjectRepository")
 */
class Project
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Phase", mappedBy="project", orphanRemoval=true)
     */
    private $phases;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @var App\Application\Sonata\MediaBundle\Entity\Media
     * @Assert\NotBlank()
     * @ORM\ManyToOne(targetEntity="App\Application\Sonata\MediaBundle\Entity\Media", cascade={"persist"}, fetch="LAZY")
     * @ORM\JoinColumn(name="background_image_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $backgroundImage;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\HotTopic", mappedBy="project", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $hotTopic;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="projects")
     * @ORM\JoinTable(name="project_user")
     */
    private $users;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActivated;

    public function __construct()
    {
        $this->phases = new ArrayCollection();
        $this->hotTopic = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        $this->setSlug($this->name);
        return $this;
    }

    /**
     * @return Collection|Phase[]
     */
    public function getPhases(): Collection
    {
        return $this->phases;
    }

    public function addPhase(Phase $phase): self
    {
        if (!$this->phases->contains($phase)) {
            $this->phases[] = $phase;
            $phase->setProject($this);
        }

        return $this;
    }

    public function removePhase(Phase $phase): self
    {
        if ($this->phases->contains($phase)) {
            $this->phases->removeElement($phase);
            // set the owning side to null (unless already changed)
            if ($phase->getProject() === $this) {
                $phase->setProject(null);
            }
        }

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $this->slugify($slug);

        return $this;
    }

    // Slug function
    public function slugify($text)
    {
       // replace non letter or digits by -
       $text = preg_replace('#[^\\pL\d]+#u', '-', $text);
       // trim
       $text = trim($text, '-');
       // transliterate
       if (function_exists('iconv'))
       {
           $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
       }
       // lowercase
       $text = strtolower($text);

       // remove unwanted characters
       $text = preg_replace('#[^-\w]+#', '', $text);

       if (empty($text))
       {
           return 'n-a';
       }

       return $text;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @param \Sonata\MediaBundle\Model\MediaInterface $backgroundImage
     */
    public function setBackgroundImage(\Sonata\MediaBundle\Model\MediaInterface $backgroundImage)
    {
        $this->backgroundImage = $backgroundImage;
    }
    /**
     * @return \Sonata\MediaBundle\Model\MediaInterface
     */
    public function getBackgroundImage()
    {
        return $this->backgroundImage;
    }

    /**
     * @return Collection|HotTopic[]
     */
    public function getHotTopic(): Collection
    {
        return $this->hotTopic;
    }

    public function addHotTopic(HotTopic $hotTopic): self
    {
        if (!$this->hotTopic->contains($hotTopic)) {
            $this->hotTopic[] = $hotTopic;
            $hotTopic->setProject($this);
        }

        return $this;
    }

    public function removeHotTopic(HotTopic $hotTopic): self
    {
        if ($this->hotTopic->contains($hotTopic)) {
            $this->hotTopic->removeElement($hotTopic);
            // set the owning side to null (unless already changed)
            if ($hotTopic->getProject() === $this) {
                $hotTopic->setProject(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getName();
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function setUsers($users)
    {
        if (count($users) > 0) {
            foreach ($users as $i) {
                $this->addUser($i);
            }
        }
        return $this;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
        }
        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);

            $arr = $user->getProjects();
            if ( empty($arr) ) {
              $user->setIsActive(false);
            }
        }
        return $this;
    }

    public function getIsActivated(): ?bool
    {
        return $this->isActivated;
    }

    public function setIsActivated(bool $isActivated): self
    {
        $this->isActivated = $isActivated;

        return $this;
    }

    // public function setUser(?User $user): self
    // {
    //     $this->user = $user;
    //
    //     return $this;
    // }
}
