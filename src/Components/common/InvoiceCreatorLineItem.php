<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Components\common;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveResponder;
use Symfony\UX\LiveComponent\ValidatableComponentTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

#[AsLiveComponent(template: 'components/common/invoice_creator_line_item.html.twig')]
class InvoiceCreatorLineItem
{
    use DefaultActionTrait;
    use ValidatableComponentTrait;

    #[LiveProp]
    public int $key;

    #[LiveProp(writable: true)]
    #[Assert\NotNull]
    public ?Product $product = null;

    #[LiveProp(writable: true)]
    #[Assert\Positive]
    public int $quantity = 1;

    #[LiveProp(writable: true)]
    #[Assert\PositiveOrZero]
    public ?float $discount = 0;


    #[LiveProp]
    public bool $isEditing = false;

    private $tokenStorage;

    public function __construct(private ProductRepository $productRepository, TokenStorageInterface $tokenStorage)
    {
        $this->productRepository = $productRepository;
        $this->tokenStorage = $tokenStorage;
    }

    public function mount(?int $productId): void
    {
        if ($productId) {
            $this->product = $this->productRepository->find($productId);
        }
    }

    #[LiveAction]
    public function save(LiveResponder $responder): void
    {
        $this->validate();

        $responder->emitUp('line_item:save', [
            'key' => $this->key,
            'product' => $this->product->getId(),
            'quantity' => $this->quantity,
            'discount' => $this->discount,
        ]);

        $this->changeEditMode(false, $responder);
    }

    private function changeEditMode(bool $isEditing, LiveResponder $responder): void
    {
        $this->isEditing = $isEditing;

        $responder->emitUp('line_item:change_edit_mode', [
            'key' => $this->key,
            'isEditing' => $this->isEditing,
        ]);
    }

    #[LiveAction]
    public function edit(LiveResponder $responder): void
    {
        $this->changeEditMode(true, $responder);
    }

    #[ExposeInTemplate]
    public function getProducts(): array
    {
        $token = $this->tokenStorage->getToken();
        $user = $token->getUser();
        return $user->getProducts()->toArray();
    }

}