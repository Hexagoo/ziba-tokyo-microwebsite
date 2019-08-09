<?php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sonata\MediaBundle\Model\MediaInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PhaseRepository")
 */
class Phase
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
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Activity", mappedBy="phase", orphanRemoval=true)
     */
    private $activities;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Project", inversedBy="phases")
     * @ORM\JoinColumn(nullable=false)
     */
    private $project;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @var App\Application\Sonata\MediaBundle\Entity\Media
     * @Assert\NotBlank()
     * @ORM\ManyToOne(targetEntity="App\Application\Sonata\MediaBundle\Entity\Media", cascade={"persist"}, fetch="LAZY")
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $image;

    /**
     * @ORM\Column(type="integer")
     */
    private $position;

    public function __construct()
    {
        $this->activities = new ArrayCollection();
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
     * @return Collection|Activity[]
     */
    public function getActivities(): Collection
    {
        return $this->activities;
    }

    public function addActivity(Activity $activity): self
    {
        if (!$this->activities->contains($activity)) {
            $this->activities[] = $activity;
            $activity->setPhase($this);
        }

        return $this;
    }

    public function removeActivity(Activity $activity): self
    {
        if ($this->activities->contains($activity)) {
            $this->activities->removeElement($activity);
            // set the owning side to null (unless already changed)
            if ($activity->getPhase() === $this) {
                $activity->setPhase(null);
            }
        }

        return $this;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): self
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    public function setSlug($slug)
    {
        $this->slug = $this->slugify($slug);
    }

    /**
     * @param \Sonata\MediaBundle\Model\MediaInterface $backgroundImage
     */
    public function setImage(\Sonata\MediaBundle\Model\MediaInterface $image)
    {
        $this->image = $image;
    }
    /**
     * @return \Sonata\MediaBundle\Model\MediaInterface
     */
    public function getImage()
    {
        return $this->image;
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

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function __toString()
    {
        return $this->getName();
    }
}
