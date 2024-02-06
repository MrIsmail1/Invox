<?php

namespace App\Entity;

use App\Repository\CompanyDetailsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompanyDetailsRepository::class)]
class CompanyDetails
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $company_logo = null;

    #[ORM\Column(length: 80, nullable: true)]
    private ?string $company_name = null;

    #[ORM\Column(length: 100)]
    private ?string $company_email = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $siret_number = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $vat_number = null;

    #[ORM\Column]
    private ?bool $vat_exempt = false;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $legal_status = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $rcs = null;

    #[ORM\Column]
    private ?float $default_vat = 20;

    #[ORM\Column(length: 30)]
    private ?string $country = "FR";

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $address = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $website = null;

    #[ORM\OneToMany(mappedBy: 'company_details', targetEntity: User::class)]
    private Collection $users;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $postal_code = null;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCompanyLogo(): ?string
    {
        return $this->company_logo;
    }

    public function setCompanyLogo(?string $company_logo): static
    {
        $this->company_logo = $company_logo;

        return $this;
    }

    public function getCompanyName(): ?string
    {
        return $this->company_name;
    }

    public function setCompanyName(?string $company_name): static
    {
        $this->company_name = $company_name;

        return $this;
    }

    public function getCompanyEmail(): ?string
    {
        return $this->company_email;
    }

    public function setCompanyEmail(string $company_email): static
    {
        $this->company_email = $company_email;

        return $this;
    }

    public function getSiretNumber(): ?string
    {
        return $this->siret_number;
    }

    public function setSiretNumber(?string $siret_number): static
    {
        $this->siret_number = $siret_number;

        return $this;
    }

    public function getVatNumber(): ?string
    {
        return $this->vat_number;
    }

    public function setVatNumber(?string $vat_number): static
    {
        $this->vat_number = $vat_number;

        return $this;
    }

    public function isVatExempt(): ?bool
    {
        return $this->vat_exempt;
    }

    public function setVatExempt(bool $vat_exempt): static
    {
        $this->vat_exempt = $vat_exempt;

        return $this;
    }

    public function getLegalStatus(): ?string
    {
        return $this->legal_status;
    }

    public function setLegalStatus(?string $legal_status): static
    {
        $this->legal_status = $legal_status;

        return $this;
    }

    public function getRcs(): ?string
    {
        return $this->rcs;
    }

    public function setRcs(?string $rcs): static
    {
        $this->rcs = $rcs;

        return $this;
    }

    public function getDefaultVat(): ?float
    {
        return $this->default_vat;
    }

    public function setDefaultVat(float $default_vat): static
    {
        $this->default_vat = $default_vat;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): static
    {
        $this->website = $website;

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
            $user->setCompanyDetails($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getCompanyDetails() === $this) {
                $user->setCompanyDetails(null);
            }
        }

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postal_code;
    }

    public function setPostalCode(?string $postal_code): static
    {
        $this->postal_code = $postal_code;

        return $this;
    }
}
