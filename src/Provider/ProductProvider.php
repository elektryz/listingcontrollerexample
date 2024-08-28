<?php

namespace inIT\ListingControllerExample\Provider;

class ProductProvider
{
    /**
     * This code comes from CategoryCore::getProducts() method.
     * I just removed id_category and id_supplier condition from there.
     * This is in order to fetch all necessary product data for listing.
     */
    public function getProducts(
        $idLang,
        $pageNumber,
        $productPerPage,
        $orderBy = null,
        $orderWay = null,
        $getTotal = false,
        $active = true,
        $random = false,
        $randomNumberProducts = 1,
        \Context $context = null
    ) {
        if (!$context) {
            $context = \Context::getContext();
        }

        $front = in_array($context->controller->controller_type, ['front', 'modulefront']);

        /* Return only the number of products */
        if ($getTotal) {
            $sql = 'SELECT COUNT(p.`id_product`) AS total
					FROM `' . _DB_PREFIX_ . 'product` p
					' . \Shop::addSqlAssociation('product', 'p') . '
					WHERE 1' .
                ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '') .
                ($active ? ' AND product_shop.`active` = 1' : '');

            return (int) \Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
        }

        if ($pageNumber < 1) {
            $pageNumber = 1;
        }

        /** Tools::strtolower is a fix for all modules which are now using lowercase values for 'orderBy' parameter */
        $orderBy = \Validate::isOrderBy($orderBy) ? \Tools::strtolower($orderBy) : 'position';
        $orderWay = \Validate::isOrderWay($orderWay) ? \Tools::strtoupper($orderWay) : 'ASC';

        $orderByPrefix = false;
        if ($orderBy === 'id_product' || $orderBy === 'date_add' || $orderBy === 'date_upd') {
            $orderByPrefix = 'p';
        } elseif ($orderBy === 'name') {
            $orderByPrefix = 'pl';
        } elseif ($orderBy === 'manufacturer' || $orderBy === 'manufacturer_name') {
            $orderByPrefix = 'm';
            $orderBy = 'name';
        } elseif ($orderBy === 'position') {
            $orderByPrefix = 'cp';
        }

        if ($orderBy === 'price') {
            $orderBy = 'orderprice';
        }

        $nbDaysNewProduct = \Configuration::get('PS_NB_DAYS_NEW_PRODUCT');
        if (!\Validate::isUnsignedInt($nbDaysNewProduct)) {
            $nbDaysNewProduct = 20;
        }

        $sql = 'SELECT DISTINCT p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) AS quantity' .
            (\Combination::isFeatureActive() ? ', IFNULL(product_attribute_shop.id_product_attribute, 0) 
            AS id_product_attribute,
					product_attribute_shop.minimal_quantity AS product_attribute_minimal_quantity' : '') . ', 
					pl.`description`, pl.`description_short`, pl.`available_now`,
					pl.`available_later`, pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`, 
					pl.`name`, image_shop.`id_image` id_image,
					il.`legend` as legend, m.`name` AS manufacturer_name, cl.`name` AS category_default,
					DATEDIFF(product_shop.`date_add`, DATE_SUB("' . date('Y-m-d') . ' 00:00:00",
					INTERVAL ' . (int) $nbDaysNewProduct . ' DAY)) > 0 AS new, product_shop.price AS orderprice
				FROM `' . _DB_PREFIX_ . 'product` p
				' . \Shop::addSqlAssociation('product', 'p') .
            (\Combination::isFeatureActive() ? ' LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_shop` 
            product_attribute_shop
				ON (p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`default_on` = 1 
				AND product_attribute_shop.id_shop=' . (int) $context->shop->id . ')' : '') . '
				' . \Product::sqlStock('p', 0) . '
				LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl
					ON (product_shop.`id_category_default` = cl.`id_category`
					AND cl.`id_lang` = ' . (int) $idLang . \Shop::addSqlRestrictionOnLang('cl') . ')
				LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl
					ON (p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = ' . (int) $idLang . \Shop::addSqlRestrictionOnLang('pl') . ')
				LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop
					ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND 
					image_shop.id_shop=' . (int) $context->shop->id . ')
				LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il
					ON (image_shop.`id_image` = il.`id_image`
					AND il.`id_lang` = ' . (int) $idLang . ')
				LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer` m
					ON m.`id_manufacturer` = p.`id_manufacturer`
				WHERE product_shop.`id_shop` = ' . (int) $context->shop->id .
            ($active ? ' AND product_shop.`active` = 1' : '')
            . ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '');

        if ($random === true) {
            $sql .= ' ORDER BY RAND() LIMIT ' . (int) $randomNumberProducts;
        } elseif ($orderBy !== 'orderprice') {
            $sql .= ' ORDER BY ' . (!empty($orderByPrefix) ? $orderByPrefix . '.' : '') . '`' . bqSQL($orderBy) .
                '` ' . pSQL($orderWay) . '
			LIMIT ' . (((int) $pageNumber - 1) * (int) $productPerPage) . ',' . (int) $productPerPage;
        }

        $result = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql, true, false);

        if (!$result) {
            return [];
        }

        if ($orderBy === 'orderprice') {
            \Tools::orderbyPrice($result, $orderWay);
            $result = array_slice($result, (int) (($pageNumber - 1) * $productPerPage), (int) $productPerPage);
        }

        // Modify SQL result
        return \Product::getProductsProperties($idLang, $result);
    }
}