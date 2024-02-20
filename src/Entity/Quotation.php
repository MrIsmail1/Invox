<?php

namespace App\Entity;

use App\Repository\QuotationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: QuotationRepository::class)]
class Quotation
{

    use Traits\Timestampable;

    public const STATUS_ACCEPTED = 'Accepté';
    public const STATUS_DENIED = 'Refusé';
    public const STATUS_DRAFT = 'Brouillon';
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 8, scale: 2, nullable: true)]
    private ?string $totalWithOutTaxe = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 8, scale: 2, nullable: true)]
    private ?string $total = null;

    #[ORM\Column(type: Types::FLOAT, nullable: true)]
    #[Assert\Range(min: 0, max: 100)]
    private ?float $taxe = 0;

    #[ORM\Column(nullable: true)]
    private ?bool $isValid = null;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'quotations')]
    private Collection $users;

    #[ORM\ManyToOne(inversedBy: 'Quotations')]
    private ?Quotation $quotation = null;

    #[ORM\ManyToOne(inversedBy: 'Quotations')]
    private ?Customer $customer = null;

    #[ORM\OneToMany(mappedBy: 'quotation', targetEntity: InvoiceItem::class, orphanRemoval: true)]
    private Collection $invoiceItems;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->invoiceItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTaxe(): ?float
    {
        return $this->taxe;
    }

    public function setTaxe(?float $taxe): self
    {
        $this->taxe = $taxe;
        return $this;
    }

    public function getTotalWithOutTaxe(): ?string
    {
        return $this->totalWithOutTaxe;
    }

    public function setTotalWithOutTaxe(?string $totalWithOutTaxe): static
    {
        $this->totalWithOutTaxe = $totalWithOutTaxe;

        return $this;
    }

    public function getTotal(): ?string
    {
        return $this->total;
    }

    public function setTotal(?string $total): static
    {
        $this->total = $total;

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
     * @return Collection<int, InvoiceItem>
     */
    public function getInvoiceItems(): Collection
    {
        return $this->invoiceItems;
    }

    public function addInvoiceItem(InvoiceItem $invoiceItem): self
    {
        if (!$this->invoiceItems->contains($invoiceItem)) {
            $this->invoiceItems->add($invoiceItem);
            $invoiceItem->setQuotation($this);
        }

        return $this;
    }

    public function removeInvoiceItem(InvoiceItem $invoiceItem): self
    {
        if ($this->invoiceItems->removeElement($invoiceItem)) {
            if ($invoiceItem->getQuotation() === $this) {
                $invoiceItem->setQuotation(null);
            }
        }

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

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): static
    {
        $this->customer = $customer;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public static function getStatusChoices()
    {
        return [
            'Accepté' => self::STATUS_ACCEPTED,
            'Refusé' => self::STATUS_DENIED,
            'Brouillon' => self::STATUS_DRAFT,
        ];
    }
    
}
