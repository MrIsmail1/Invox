<?php

namespace App\Entity;

use App\Repository\InvoiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: InvoiceRepository::class)]
class Invoice
{
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /* #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $customerName = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Email]
    private ?string $customerEmail = null; */

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
    #[Assert\Range(min: 0, max: 100)]
    private ?string $taxes = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 8, scale: 2, nullable: true)]
    private ?string $totalWithTaxes = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isValid = null;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'invoices')]
    private Collection $users;

    #[ORM\ManyToOne(inversedBy: 'Invoices')]
    private ?Quotation $quotation = null;

    #[ORM\ManyToOne(inversedBy: 'Invoices')]
    private ?Customer $customer = null;

    #[ORM\OneToMany(mappedBy: 'invoice', targetEntity: InvoiceItem::class, orphanRemoval: true)]
    private Collection $invoiceItems;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->invoiceItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
            $invoiceItem->setInvoice($this);
        }

        return $this;
    }

    public function removeInvoiceItem(InvoiceItem $invoiceItem): self
    {
        if ($this->invoiceItems->removeElement($invoiceItem)) {
            // set the owning side to null (unless already changed)
            if ($invoiceItem->getInvoice() === $this) {
                $invoiceItem->setInvoice(null);
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
            $user->addInvoice($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            $user->removeInvoice($this);
        }

        return $this;
    }

    public function getQuotation(): ?Quotation
    {
        return $this->quotation;
    }

    public function setQuotation(?Quotation $quotation): static
    {
        $this->quotation = $quotation;

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

    public function __toString(): string
    {
        return $this->id;
    }
}
