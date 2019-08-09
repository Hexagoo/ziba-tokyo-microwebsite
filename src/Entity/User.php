<?php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table(name="user")
 * @UniqueEntity(fields="projectName")
 * @ORM\Entity()
 */
class User implements UserInterface, \Serializable {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=64, unique=true)
     * @Assert\NotBlank()
     */
    private $projectName;

    /**
     * @Assert\Length(max=250)
     */
    private $plainPassword;

    /**
     * The below length depends on the "algorithm" you use for encoding
     * the password, but this works well with bcrypt.
     *
     * @ORM\Column(type="string", length=64)
     */
    private $password;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(name="roles", type="array")
     */
    private $roles = array();

    private $newPass;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Project", mappedBy="users")
     */
    private $projects;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\ActivityHasMedia", mappedBy="users")
     */
    private $activityHasMedias;

    /**
     * @ORM\Column(type="boolean")
     */
    private $newUser;

    public function getNewPass() {
       return $this->newPass;
    }

    public function setNewPass($newPass) {
       $this->newPass = $newPass;
       return $this;
    }

    public function __construct() {
        $this->isActive = false;
        $this->projects = new ArrayCollection();
        $this->activityHasMedias = new ArrayCollection();
        $this->newUser = false;
        // may not be needed, see section on salt below
        // $this->salt = md5(uniqid('', true));
    }

    public function getUsername() {
        return $this->projectName;
    }

    public function getSalt() {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return null;
    }

    public function getPassword() {
        return $this->password;
    }

    function setPassword($password) {
        $this->password = $password;
    }

    public function getRoles() {
        if (empty($this->roles)) {
            $roles[] = 'ROLE_USER';

            return array_unique($roles);
        }
        return $this->roles;
    }

    public function setRoles(array $roles)
    {
        $this->roles = $roles;

        // allows for chaining
        return $this;
    }

    function addRole($role) {
        $this->roles[] = $role;
    }

    public function eraseCredentials() {

    }

    /** @see \Serializable::serialize() */
    public function serialize() {
        return serialize(array(
            $this->id,
            $this->projectName,
            $this->password,
            $this->isActive,
                // see section on salt below
                // $this->salt,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized) {
        list (
                $this->id,
                $this->projectName,
                $this->password,
                $this->isActive,
                // see section on salt below
                // $this->salt
                ) = unserialize($serialized);
    }

    function getId() {
        return $this->id;
    }

    function getProjectName() {
        return $this->projectName;
    }

    function getPlainPassword() {
        return $this->plainPassword;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setProjectName($projectName) {
        $this->projectName = $projectName;
    }

    function setPlainPassword($plainPassword) {
        $this->plainPassword = $plainPassword;
    }

    function getIsActive() {
        return $this->isActive;
    }

    function setIsActive($isActive) {
        $this->isActive = $isActive;
    }

    /**
     * @return Collection|Project[]
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function setProjects($projects)
    {
        if (count($projects) > 0) {
            foreach ($projects as $i) {
                $this->addProject($i);
            }
        }
        return $this;
    }

    /**
     * @return Collection|ActivityHasMedia[]
     */
    public function getActivityHasMedias(): Collection
    {
        return $this->activityHasMedias;
    }

    public function setActivityHasMedias($activityHasMedias)
    {
        if (count($activityHasMedias) > 0) {
            foreach ($activityHasMedias as $i) {
                $this->addActivityHasMedias($i);
            }
        }
        return $this;
    }

    public function addActivityHasMedia(ActivityHasMedia $activityHasMedia): self
    {
        $this->activityHasMedias[] = $activityHasMedia;
        return $this;
    }

    public function removeActivityHasMedia(ActivityHasMedia $activityHasMedia): self
    {
        if ($this->activityHasMedias->contains($activityHasMedia)) {
            $this->activityHasMedias->removeElement($activityHasMedia);
            // set the owning side to null (unless already changed)
            if ($activityHasMedia->getUsers() === $this) {
                $activityHasMedia->setUser(null);
            }
        }
        return $this;
    }

    public function addProject(Project $project): self
    {
        if (!$this->projects->contains($project)) {
            $this->projects[] = $project;
            $project->setUsers($this);
        }
        return $this;
    }

    public function removeProject(Project $project): self
    {
        if ($this->projects->contains($project)) {
            $this->projects->removeElement($project);
            // set the owning side to null (unless already changed)
            if ($project->getUsers() === $this) {
                $project->setUser(null);
            }
        }
        if(count($this->getProjects()) == 0) {
          $this->setIsActive(false);
        }
        return $this;
    }

    public function getNewUser(): ?bool
    {
        return $this->newUser;
    }

    public function setNewUser(bool $newUser): self
    {
        $this->newUser = $newUser;

        return $this;
    }

    public function __toString()
    {
        return $this->getUsername();
    }
}
