<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

class listingcontrollerexample extends Module
{
    public function __construct()
    {
        $this->name = 'listingcontrollerexample';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'inIT Kamil Góralczyk';
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->trans(
            'Listing controller example',
            [],
            'Modules.Listingcontrollerexample.Admin'
        );

        $this->description = $this->trans(
            'Help developers to understand how to create module with front listing controller.',
            [],
            'Modules.Listingcontrollerexample.Admin'
        );

        $this->ps_versions_compliancy = ['min' => '8.1.0', 'max' => _PS_VERSION_];
    }

    public function isUsingNewTranslationSystem()
    {
        return true;
    }

    public function getContent()
    {
        return $this->context->link->getModuleLink($this->name, 'view');
    }
}