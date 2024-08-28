<?php

use inIT\ListingControllerExample\Configuration\ModuleConfiguration;
use inIT\ListingControllerExample\Provider\SearchProvider;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;

class ListingcontrollerexampleViewModuleFrontController extends ProductListingFrontController
{
    public function __construct()
    {
        parent::__construct();

        $this->controller_type = 'modulefront';
    }

    public function initContent()
    {
        parent::initContent();

        $this->context->smarty->assign([
            'productClassList' => ModuleConfiguration::PRODUCT_CLASS,
        ]);

        $this->doProductSearch(
            '../../../modules/'.ModuleConfiguration::MODULE_NAME.'/views/templates/pages/products-list.tpl',
            [
                'entity' => ModuleConfiguration::MODULE_NAME,
            ]
        );
    }

    public function getTemplateVarPage()
    {
        $page = parent::getTemplateVarPage();

        $page['meta']['title'] = $this->getListingLabel();

        return $page;
    }

    protected function getProductSearchQuery()
    {
        $query = new ProductSearchQuery();
        $query
            ->setQueryType(ModuleConfiguration::MODULE_NAME)
            ->setSortOrder(new SortOrder('product', 'name', 'asc'));

        return $query;
    }

    protected function getDefaultProductSearchProvider()
    {
        return new SearchProvider($this->getTranslator());
    }

    public function getListingLabel()
    {
        return $this->trans('Listing example', [], 'Modules.Listingcontrollerexample.Front');
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $breadcrumb['links'][] = [
            'title' => $this->trans('Listing example', [], 'Modules.Listingcontrollerexample.Front'),
            'url' => $this->context->link->getModuleLink('listingcontrollerexample', 'view'),
        ];

        return $breadcrumb;
    }

    public function getAjaxProductSearchVariables()
    {
        $this->context->smarty->assign('productClass', ModuleConfiguration::PRODUCT_CLASS);

        return parent::getAjaxProductSearchVariables();
    }
}
