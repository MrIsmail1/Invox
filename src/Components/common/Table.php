<?php

// src/Twig/Components/Alert.php
namespace App\Components\common;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class Table
{
    public ?string $thead1 = null;
    public ?string $thead2 = null;
    public ?string $thead3 = null;
    public ?string $thead4 = null;
    public ?string $thead5 = null;
    public ?string $thead6 = null;
    public ?string $thead7 = null;
    public ?string $pathDelete = null;
    public ?string $pathEdit = null;
    public $data = null;
    public $form = null;
    public $entity = null;
}