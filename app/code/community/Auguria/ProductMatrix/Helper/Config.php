<?php
/**
 * @category   Auguria
 * @package    Auguria_ProductMatrix
 * @author     Auguria
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3 (GPLv3)
 */
 
class Auguria_ProductMatrix_Helper_Config extends Mage_Core_Helper_Abstract {

    /* PATH TO CONFIGURATION */
    const XML_PATH_PRODUCT_MATRIX_IS_ACTIVE    = 'auguria_productmatrix/general/is_active';
    const XML_PATH_PRODUCT_MATRIX_GROUPS       = 'auguria_productmatrix/general/groups';
    const XML_PATH_PRODUCT_MATRIX_TEMPLATE     = 'auguria_productmatrix/general/template';

    /**
     * Check if the display of the matrix is active
     *
     * @return bool
     */
    public function isProductMatrixActive(){
        return Mage::getStoreConfigFlag(self::XML_PATH_PRODUCT_MATRIX_IS_ACTIVE);
    }

    /**
     * Get customer groups authorized by config
     *
     * @param bool $asArray
     * @return string
     */
    public function getCustomerGroups($asArray = false){
        $groupIds = Mage::getStoreConfig(self::XML_PATH_PRODUCT_MATRIX_GROUPS);

        if($asArray){
            return explode(',', $groupIds); // array
        }

        return $groupIds; // string with ids comma separated
    }

    /**
     * Get selected template in config
     *
     * @return string
     */
    public function getMatrixTemplate(){
        return Mage::getStoreConfig(self::XML_PATH_PRODUCT_MATRIX_TEMPLATE);
    }
}