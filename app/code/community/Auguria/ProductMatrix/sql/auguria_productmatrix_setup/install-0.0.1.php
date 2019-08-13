<?php
/**
 * @category   Auguria
 * @package    Auguria_ProductMatrix
 * @author     Auguria
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3 (GPLv3)
 */

/* @var $this Mage_Catalog_Model_Resource_Setup */

// Create new attribute group ( render attribute element correctly )
$entityTypeId = $this->getEntityTypeId('catalog_product');

// add group for each existing attribute set
$attributeSets = $this->_conn->fetchAll('select * from '.$this->getTable('eav/attribute_set').' where entity_type_id=?', $entityTypeId);
foreach ($attributeSets as $attributeSet) {
    $setId = $attributeSet['attribute_set_id'];
    $this->addAttributeGroup($entityTypeId, $setId, 'Product matrix');
}

// only for configurable products
$productTypes = array(
        Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE
);
$productTypes = join(',', $productTypes);

// auguria_matrix_active : define if the matrix can be displayed on the product view page
$this->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'auguria_matrix_active', array(
        'group'             => 'Product matrix',
        'backend'           => 'catalog/product_attribute_backend_boolean',
        'frontend'          => '',
        'label'             => 'Active matrix on the product view page',
        'input'             => 'select',
        'source'            => 'eav/entity_attribute_source_boolean',
        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
        'visible'           => true,
        'required'          => false,
        'user_defined'      => false,
        'default'           => '',
        'apply_to'          => $productTypes,
        'input_renderer'    => 'auguria_productmatrix/adminhtml_catalog_product_helper_form_config',
        'visible_on_front'  => false,
        'used_in_product_listing' => true
));

// auguria_matrix_customer_groups : define the customer groups that can be displayed on the product view page
$this->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'auguria_matrix_customer_groups', array(
        'group'             => 'Product matrix',
        'backend'           => 'auguria_productmatrix/entity_attribute_backend_customer_group',
        'frontend'          => '',
        'label'             => 'Customer groups authorized to display matrix on the product view page',
        'input'             => 'multiselect',
        'type'              => 'varchar',
        'source'            => 'auguria_core/customer_attribute_source_group',
        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
        'visible'           => true,
        'required'          => false,
        'user_defined'      => false,
        'default'           => '',
        'apply_to'          => $productTypes,
        'input_renderer'    => 'auguria_productmatrix/adminhtml_catalog_product_helper_form_config_customer_group',
        'visible_on_front'  => false,
        'used_in_product_listing' => true
));
