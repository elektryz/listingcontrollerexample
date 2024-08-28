{extends file='catalog/listing/product-list.tpl'}

{*
 * This is not necessary, but its used to display 4 products per row to match full page width.
 * You can remove this and refresh front controller page to see the difference.
 *}
{block name='product_list'}
    {include file='catalog/_partials/products.tpl' listing=$listing productClass="{$productClassList}"}
{/block}