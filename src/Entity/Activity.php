<?php

namespace App\Entity;

use App\Application\Sonata\MediaBundle\Entity\Media;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ActivityRepository")
 */
class Activity
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank()
     * @ORM\OneToMany(targetEntity="App\Entity\ActivityHasMedia", mappedBy="activity",cascade={"persist","remove"} )
     */
    protected $activityHasMedias;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Phase", inversedBy="activities")
     * @ORM\JoinColumn(nullable=false)
     */
    private $phase;

    /**
     * @ORM\Column(type="integer")
     */
    private $position;

    public function __construct()
    {
        $this->activityHasMedias = new ArrayCollection();
    }

    /**
     * Remove widgetImages
     *
     * @param \App\Application\Sonata\MediaBundle\Entity\Media $widgetImages
     */
    public function removeActivityHasMedias(\App\Entity\ActivityHasMedia $activityHasMedias)
    {
        $this->activityHasMedias->removeElement($activityHasMedias);
    }

    /**
     * Get widgetImages
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getActivityHasMedias()
    {
        return $this->activityHasMedias;
    }

    /**
     * {@inheritdoc}
     */
    public function setActivityHasMedias($activityHasMedias)
    {
        $this->activityHasMedias = new ArrayCollection();

        foreach ($activityHasMedias as $activity) {
            $this->addActivityHasMedias($activity);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addActivityHasMedias(\App\Entity\ActivityHasMedia $activityHasMedias)
    {
        $activityHasMedias->setActivity($this);

        $this->activityHasMedias[] = $activityHasMedias;
    }

    public function getId()
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

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPhase(): ?Phase
    {
        return $this->phase;
    }

    public function setPhase(?Phase $phase): self
    {
        $this->phase = $phase;

        return $this;
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
}
