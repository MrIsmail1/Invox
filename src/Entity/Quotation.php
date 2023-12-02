<?php

namespace App\Entity;

use App\Repository\QuotationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuotationRepository::class)]
class Quotation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $expiresIn = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 8, scale: 2, nullable: true)]
    private ?string $amount = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $option = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 8, scale: 2, nullable: true)]
    private ?string $optionPrice = null;

    #[ORM\Column(nullable: true)]
    private ?int $quantity = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 8, scale: 2, nullable: true)]
    private ?string $totalWithOutTaxes = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 4, scale: 2, nullable: true)]
    private ?string $taxes = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 8, scale: 2, nullable: true)]
    private ?string $totalWithTaxes = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isValid = null;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'quotations')]
    private Collection $users;

    #[ORM\OneToMany(mappedBy: 'quotation', targetEntity: Invoice::class)]
    private Collection $Invocies;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->Invocies = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getExpiresIn(): ?\DateTimeInterface
    {
        return $this->expiresIn;
    }

    public function setExpiresIn(?\DateTimeInterface $expiresIn): static
    {
        $this->expiresIn = $expiresIn;

        return $this;
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

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(?string $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getOption(): ?string
    {
        return $this->option;
    }

    public function setOption(?string $option): static
    {
        $this->option = $option;

        return $this;
    }

    public function getOptionPrice(): ?string
    {
        return $this->optionPrice;
    }

    public function setOptionPrice(?string $optionPrice): static
    {
        $this->optionPrice = $optionPrice;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getTotalWithOutTaxes(): ?string
    {
        return $this->totalWithOutTaxes;
    }

    public function setTotalWithOutTaxes(?string $totalWithOutTaxes): static
    {
        $this->totalWithOutTaxes = $totalWithOutTaxes;

        return $this;
    }

    public function getTaxes(): ?string
    {
        return $this->taxes;
    }

    public function setTaxes(?string $taxes): static
    {
        $this->taxes = $taxes;

        return $this;
    }

    public function getTotalWithTaxes(): ?string
    {
        return $this->totalWithTaxes;
    }

    public function setTotalWithTaxes(?string $totalWithTaxes): static
    {
        $this->totalWithTaxes = $totalWithTaxes;

        return $this;
    }

    public function isIsValid(): ?bool
    {
        return $this->isValid;
    }

    public function setIsValid(?bool $isValid): static
    {
        $this->isValid = $isValid;

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
            $user->addQuotation($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            $user->removeQuotation($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Invoice>
     */
    public function getInvocies(): Collection
    {
        return $this->Invocies;
    }

    public function addInvocy(Invoice $invocy): static
    {
        if (!$this->Invocies->contains($invocy)) {
            $this->Invocies->add($invocy);
            $invocy->setQuotation($this);
        }

        return $this;
    }

    public function removeInvocy(Invoice $invocy): static
    {
        if ($this->Invocies->removeElement($invocy)) {
            // set the owning side to null (unless already changed)
            if ($invocy->getQuotation() === $this) {
                $invocy->setQuotation(null);
            }
        }

        return $this;
    }
}
