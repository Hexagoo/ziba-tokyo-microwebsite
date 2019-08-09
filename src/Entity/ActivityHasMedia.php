<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Sonata\MediaBundle\Model\MediaInterface;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ActivityHasMediaRepository")
 */
class ActivityHasMedia
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var App\Application\Sonata\MediaBundle\Entity\Media
     * @Assert\NotBlank()
     * @ORM\ManyToOne(targetEntity="App\Application\Sonata\MediaBundle\Entity\Media", cascade={"persist"}, fetch="LAZY")
     * @ORM\JoinColumn(name="media_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $media;

    /**
    * @var \App\Entity\Activity
    * @ORM\ManyToOne(targetEntity="App\Entity\Activity", inversedBy="activityHasMedias", cascade={"persist"})
    * @ORM\JoinColumn(name="activity_id", referencedColumnName="id", nullable=true, nullable=true, onDelete="SET NULL")
    */
    private $activity;

    /**
     * @var App\Application\Sonata\MediaBundle\Entity\Media
     * @ORM\ManyToOne(targetEntity="App\Application\Sonata\MediaBundle\Entity\Media", cascade={"persist"}, fetch="LAZY")
     * @ORM\JoinColumn(name="thumbnail_image_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $thumbnailImage;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="activityHasMedias", cascade={"persist"})
     * @ORM\JoinTable(
     *     name="activityHasMedias_user",
     *     joinColumns={
     *          @ORM\JoinColumn(name="activityHasMedias_id", referencedColumnName="id")
     *     },
     *     inverseJoinColumns={
     *          @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     *     }
     * )
     */
    private $users;

    /**
     * @ORM\Column(type="boolean")
     */
    private $enabled;

    /**
     * @var boolean
     * @ORM\Column(name="delete_media", type="boolean")
     */
    protected $deleteMedia;

    public function __construct()
    {
        $this->enabled = false;
        $this->deleteMedia = false;
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getActivity()
    {
        return $this->activity;
    }

    public function setActivity(\App\Entity\Activity $activity = null)
    {
        $this->activity = $activity;

        return $this;
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
        $this->users[] = $user;
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

    /**
     * @param \Sonata\MediaBundle\Model\MediaInterface $thumbnailImage
     */
    public function setThumbnailImage(\Sonata\MediaBundle\Model\MediaInterface $thumbnailImage = null)
    {
        $this->thumbnailImage = $thumbnailImage;
    }
    /**
     * @return \Sonata\MediaBundle\Model\MediaInterface
     */
    public function getThumbnailImage()
    {
        return $this->thumbnailImage;
    }

    /**
     * {@inheritdoc}
     */
    public function setMedia(\Sonata\MediaBundle\Model\MediaInterface $media = null)
    {
        $this->media = $media;
    }
    /**
     * {@inheritdoc}
     */
    public function getMedia()
    {
        return $this->media;
    }

    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setDeleteMedia($deleteMedia)
    {
        $this->deleteMedia = $deleteMedia;
    }
    /**
     * {@inheritdoc}
     */
    public function getDeleteMedia()
    {
        return $this->deleteMedia;
    }
}
