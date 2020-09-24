<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Validator\Constraints as Assert;
// Ajoutez ce use pour le contexte
use Symfony\Component\Validator\Context\ExecutionContextInterface;
// On rajoute ce use pour la contrainte :
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use App\Validator\Antiflood;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Table(name="oc_advert")
 * @ORM\Entity(repositoryClass="App\Repository\AdvertRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(fields="title", message="Une annonce existe déjà avec ce titre.")
 */
class Advert
{
    
    private $user;

    public function __construct()
    {
        // Par défaut, la date de l'annonce est la date d'aujourd'hui
        $this->date = new \Datetime();
        $this->applications = new ArrayCollection();
        $this->categories = new ArrayCollection();

        //$this->ip = "123456";
    }
    
    /**
     * @ORM\Column(name="published", type="boolean")
     */
    private $published = true;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\DateTime()
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * Et pour être logique, il faudrait aussi mettre la colonne titre en Unique pour Doctrine :
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\Length(min=10)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=2)
     */
    private $author;

    /**
     * @ORM\Column(type="string", length=180, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     * @Antiflood()
     */
    private $content;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Image", cascade={"persist", "remove"})
     * @Assert\Valid()
     */
    private $image;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Application", mappedBy="advert")
     */
    private $applications; // Notez le « s », une annonce est liée à plusieurs candidatures
    
    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Category", cascade={"persist"})
     * @ORM\JoinTable(name="oc_advert_category")
     */
    private $categories;

    /**
     * @ORM\Column(name="nb_applications", type="integer")
     */
    private $nbApplications = 0;

    /**
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(name="slug", type="string", length=255, unique=true)
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=180, nullable=true)
     */
    private $ip;

    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(string $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getPublished(): ?bool
    {
        return $this->published;
    }

    public function setPublished(bool $published): self
    {
        $this->published = $published;

        return $this;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function setImage(Image $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection|Application[]
     */
    public function getApplications(): Collection
    {
        return $this->applications;
    }

    public function addApplication(Application $application): self
    {
        if (!$this->applications->contains($application)) {
            $this->applications[] = $application;
            $application->setAdvert($this);
        }

        return $this;
    }

    public function removeApplication(Application $application): self
    {
        if ($this->applications->contains($application)) {
            $this->applications->removeElement($application);
            // set the owning side to null (unless already changed)
            if ($application->getAdvert() === $this) {
                $application->setAdvert(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Category[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if ($this->categories->contains($category)) {
            $this->categories->removeElement($category);
        }

        return $this;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt($updatedAt):self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    //methode callback lorsque l'annonce est modifié on met à jour automatiquement la date de modification
    public function updateDate()
    {
        $this->setUpdatedAt(new \Datetime());
    }

    /**
     * Get the value of nbApplications
     */ 
    public function getNbApplications()
    {
        return $this->nbApplications;
    }

    /**
     * Set the value of nbApplications
     */ 
    public function setNbApplications($nbApplications):self
    {
        $this->nbApplications = $nbApplications;

        return $this;
    }

    //A chaque fois qu'une candidature est ajouter on incrémente la variable : nombre de candidature pour chaque annonce
    public function increaseApplication()
    {
        $this->nbApplications++;
    }

    //A chaque fois qu'une candidature est supprimer on déincrémente la variable : nombre de candidature pour chaque annonce
    public function decreaseApplication()
    {
        $this->nbApplications--;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function setSlug($slug): self
    {
        $this->slug = $slug;

        return $this;
    }
    /**
     * @Assert\Callback
     */
    public function isContentValid(ExecutionContextInterface $context)
    {
        $forbiddenWords = array('démotivation', 'abandon');

        // On vérifie que le contenu ne contient pas l'un des mots
        if (preg_match('#' . implode('|', $forbiddenWords) . '#', $this->getContent())) {
            // La règle est violée, on définit l'erreur
            $context
                ->buildViolation('Contenu invalide car il contient un mot interdit.') // message
                ->atPath('content')                                                   // attribut de l'objet qui est violé
                ->addViolation() // ceci déclenche l'erreur, ne l'oubliez pas
            ;
        }
    }

    /**
     * Get the value of ip
     */ 
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set the value of ip
     *
     * @return  self
     */ 
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * @ORM\PrePersist
     */
    public function addIpComuter()
    {
        //$myIp = $this->request->getClientIp();
        $this->ip = 00000000000;
    }

}

