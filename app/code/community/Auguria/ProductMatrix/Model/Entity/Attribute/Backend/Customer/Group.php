<?php
/**
 * @category   Auguria
 * @package    Auguria_ProductMatrix
 * @author     Auguria
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3 (GPLv3)
 */

class Auguria_ProductMatrix_Model_Entity_Attribute_Backend_Customer_Group extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Prepare data for save
     *
     * @param Varien_Object $object
     * @return Mage_Eav_Model_Entity_Attribute_Backend_Abstract
     */
    public function beforeSave($object)
    {
        $attributeCode = $this->getAttribute()->getAttributeCode();
        $data = $object->getData($attributeCode);

        if ($object->getData('use_config_' . $attributeCode)) {
            $object->setData($attributeCode, '');
        }elseif (is_array($data)) {
//             $data = array_filter($data); otherwise delete NOT LOGGED IN customer group (value 0)
            $object->setData($attributeCode, implode(',', $data));
        }

        return parent::beforeSave($object);
    }
}
