<?php

namespace App\Entity;

use App\Repository\AccountingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AccountingRepository::class)]
class Accounting
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $totalQuotations = null;

    #[ORM\Column(nullable: true)]
    private ?int $totalInvoices = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $totalIncome = null;

    #[ORM\Column(nullable: true)]
    private ?int $totalPaidInvoices = null;

    #[ORM\Column(nullable: true)]
    private ?int $totalValidQuotations = null;

    #[ORM\Column(nullable: true)]
    private ?int $totalLateInvoices = null;

    #[ORM\Column(nullable: true)]
    private ?int $totalNewCustomers = null;

    #[ORM\OneToMany(mappedBy: 'accountings', targetEntity: User::class)]
    private Collection $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTotalOfQuotation(): ?int
    {
        return $this->totalQuotations;
    }

    public function setTotalOfQuotation(?int $totalQuotations): static
    {
        $this->totalQuotations = $totalQuotations;

        return $this;
    }

    public function getTotalInvoices(): ?int
    {
        return $this->totalInvoices;
    }

    public function setTotalInvoices(?int $totalInvoices): static
    {
        $this->totalInvoices = $totalInvoices;

        return $this;
    }

    public function getTotalIncome(): ?string
    {
        return $this->totalIncome;
    }

    public function setTotalIncome(?string $totalIncome): static
    {
        $this->totalIncome = $totalIncome;

        return $this;
    }

    public function getTotalPaidInvoices(): ?int
    {
        return $this->totalPaidInvoices;
    }

    public function setTotalPaidInvoices(?int $totalPaidInvoices): static
    {
        $this->totalPaidInvoices = $totalPaidInvoices;

        return $this;
    }

    public function getTotalValidQuotations(): ?int
    {
        return $this->totalValidQuotations;
    }

    public function setTotalValidQuotations(?int $totalValidQuotations): static
    {
        $this->totalValidQuotations = $totalValidQuotations;

        return $this;
    }

    public function getTotalLateInvoices(): ?int
    {
        return $this->totalLateInvoices;
    }

    public function setTotalLateInvoices(?int $totalLateInvoices): static
    {
        $this->totalLateInvoices = $totalLateInvoices;

        return $this;
    }

    public function getTotalNewCustomers(): ?int
    {
        return $this->totalNewCustomers;
    }

    public function setTotalNewCustomers(?int $totalNewCustomers): static
    {
        $this->totalNewCustomers = $totalNewCustomers;

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
            $user->setAccountings($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getAccountings() === $this) {
                $user->setAccountings(null);
            }
        }

        return $this;
    }
}
