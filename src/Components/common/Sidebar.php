<?php

// src/Twig/Components/Alert.php
namespace App\Components\common;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;


#[AsTwigComponent]
class Sidebar
{

    public array $headerItems = [
        [
            'name' => 'Dashboard',
            'icon' => 'build/images/dashboard.svg',
            'route' => '/',
        ],
        [
            'name' => 'Devis',
            'icon' => 'build/images/devis.svg',
            'route' => '/quotation',
        ],
        [
            'name' => 'Factures',
            'icon' => 'build/images/factures.svg',
            'route' => '/invoice',
        ],
        [
            'name' => 'Clients',
            'icon' => 'build/images/clients.svg',
            'route' => '/clients',
        ],
        [
            'name' => 'Services',
            'icon' => 'build/images/services.svg',
            'route' => '/services',
        ],
    ];

    public array $footerItems = [
        [
            'name' => 'Paramètres',
            'icon' => 'build/images/parametres.svg',
            'route' => '/settings/company_details',
        ],
        [
            'name' => 'Déconnexion',
            'icon' => 'build/images/deconnect.svg',
            'route' => '/logout',
        ],
    ];
    public array $settingsHeaderItems = [
        [
            'name' => 'Cordonnées de l\'entreprise',
            'icon' => '',
            'route' => '/settings/company_details',
        ],
        [
            'name' => 'Profil utilisateur',
            'icon' => '',
            'route' => '/settings/user_profile',
        ],
        [
            'name' => 'Moyens de paiement',
            'icon' => '',
            'route' => '',
        ],
        [
            'name' => 'Rappels de paiement',
            'icon' => '',
            'route' => '',
        ],
    ];

    public array $settingsFooterItems = [
        [
            'name' => 'Accueil',
            'icon' => '',
            'route' => '/',
        ],
        [
            'name' => 'Déconnexion',
            'icon' => 'build/images/deconnect.svg',
            'route' => '/logout',
        ],
    ];

}