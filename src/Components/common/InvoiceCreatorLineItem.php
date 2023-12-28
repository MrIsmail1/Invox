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

use App\Entity\Service;
use App\Repository\ServiceRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveResponder;
use Symfony\UX\LiveComponent\ValidatableComponentTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsLiveComponent(template: 'components/common/invoice_creator_line_item.html.twig')]
class InvoiceCreatorLineItem
{
    use DefaultActionTrait;
    use ValidatableComponentTrait;

    #[LiveProp]
    public int $key;

    /* A MODIFIER */
    #[LiveProp(writable: true, useSerializerForHydration: true)]
    #[Assert\NotNull]
    public ?Service $service = null;

    #[LiveProp(writable: true)]
    #[Assert\Positive]
    public int $quantity = 1;

    #[LiveProp]
    public bool $isEditing = false;

    public function __construct(private ServiceRepository $serviceRepository)
    {
    }

    /* PROBLEME ICI */
    public function mount(?int $serviceId): void
    {
        if ($serviceId) {
            $this->service = $this->serviceRepository->find($serviceId);
        }
    }

    #[LiveAction]
    public function save(LiveResponder $responder): void
    {
        $this->validate();
        dd("test");
        $responder->emitUp('line_item:save', [
            'key' => $this->key,
            'service' => $this->service->getId(),
            'quantity' => $this->quantity,
        ]);

        $this->changeEditMode(false, $responder);
    } 

     #[LiveAction]
    public function edit(LiveResponder $responder): void
    {
        dd("test");
        $this->changeEditMode(true, $responder);
    }
 
    #[ExposeInTemplate]
    public function getService(): array
    {
        return $this->serviceRepository->findAll();
    }

    private function changeEditMode(bool $isEditing, LiveResponder $responder): void
    {
        $this->isEditing = $isEditing;
        dd("test");
        // emit to InvoiceCreator so it can track which items are being edited
        $responder->emitUp('line_item:change_edit_mode', [
            'key' => $this->key,
            'isEditing' => $this->isEditing,
        ]);
    }
}
