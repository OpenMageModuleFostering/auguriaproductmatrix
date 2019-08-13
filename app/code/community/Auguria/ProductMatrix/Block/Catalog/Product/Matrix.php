<?php
/**
 * @category   Auguria
* @package    Auguria_ProductMatrix
* @author     Auguria
* @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3 (GPLv3)
*/

class Auguria_ProductMatrix_Block_Catalog_Product_Matrix extends Mage_Core_Block_Template {

    protected $_childrenProducts;
    protected $_matrixInformations;
    protected $_productsByOption = array();

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        // check configuration and product custom options
        if (!$this->getTemplate() || !$this->canDisplayMatrix()) {
            return '';
        }
        $html = $this->renderView();
        return $html;
    }

    /**
     * Retrieve current product model
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return Mage::registry('product');
    }

    /**
     * Check if the matrix can be displayed
     *
     * @return bool
     */
    public function canDisplayMatrix(){
        $active = $this->getProduct()->getAuguriaMatrixActive() || is_null($this->getProduct()->getAuguriaMatrixActive()) && Mage::helper('auguria_productmatrix/config')->isProductMatrixActive() ? true : false;
        $groups = !is_null($this->getProduct()->getAuguriaMatrixCustomerGroups()) ? explode(',', $this->getProduct()->getAuguriaMatrixCustomerGroups()) : Mage::helper('auguria_productmatrix/config')->getCustomerGroups(true);

        return
            $active
            && !$this->getProduct()->getProductOptionsCollection()->getSize() // custom options are not managed in matrix
            && in_array(Mage::getSingleton('customer/session')->getCustomerGroupId(), $groups); // filter by customer group
    }

    /**
     * Get form url with parameters
     *
     * @return string
     */
    public function getAddUrl(){
        $addUrlKey = Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED;
        $addUrlValue = Mage::getUrl('*/*/*', array('_use_rewrite' => true, '_current' => true));
        $additional[$addUrlKey] = Mage::helper('core')->urlEncode($addUrlValue);
        $additional['product'] = $this->getProduct()->getId();

        return Mage::getUrl('auguria_productmatrix/checkout_cart/add', $additional);
    }

    /**
     * Get configurable attributes informations
     *
     * @return array
     */
    public function getMatrixInformations()
    {
        if(is_null($this->_matrixInformations)){
            $this->_matrixInformations = array();
            foreach($this->getProduct()->getTypeInstance()->getConfigurableAttributesAsArray($this->getProduct()) as $_attribute){
                $positions = array();
                foreach($_attribute['values'] as $_key => $_value){
                    $positions[$_value['value_index']] = $_key;
                }

                // add position information (js object constraint) for each attribute option
                $_attribute['matrix_option_id_to_position'] = $positions;
                // assign information
                $this->_matrixInformations[] = $_attribute;
            }
        }

        return $this->_matrixInformations;
    }

    /**
     * Get children of the configurable product
     *
     * @return array
     */
    public function getChildrenProducts()
    {
        // prepare children for js object
        if(is_null($this->_childrenProducts)){
            $this->_childrenProducts = array();
            foreach ($this->getProduct()->getTypeInstance()->getUsedProducts($this->getProduct()) as $_product){
                $this->_childrenProducts[$_product->getId()] = $_product->getData();

                $this->_prepareProductsByOption($_product);
            }
        }

        return $this->_childrenProducts;
    }

    /**
     * Prepare products by option sorted by order
     *
     * @param Mage_Catalog_Model_Product $product
     * @return void
     */
    protected function _prepareProductsByOption(Mage_Catalog_Model_Product $product)
    {
        $attributes = $this->getMatrixInformations();
        $lastAttribute = end($attributes);
        foreach (array_flip($lastAttribute['matrix_option_id_to_position']) as $_optionId){
            if($product->getData($lastAttribute['attribute_code']) == $_optionId){
                $this->_productsByOption[$_optionId][] = $product->getId();
            }
        }
    }

    /**
     * Get products id by last attribute options
     *
     * @return array
     */
    public function getProductsByOption()
    {
        return $this->_productsByOption;
    }

}