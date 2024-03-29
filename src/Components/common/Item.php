<?php

// src/Twig/Components/Alert.php
namespace App\Components\common;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class Item
{
    public string $ul;
    public string $li="";
    public string $icon;
    public string $linkUl="#";
    public string $linkLi="#";
    public string $type = "inactive";


    public function getIcon(): string
    {
        return match ($this->type) {
            'active' => 'circle-check',
            'inactive' => 'circle-exclamation',
        };
    }   
}