<?php
/**
 * @category   Auguria
 * @package    Auguria_Core
 * @author     Auguria
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3 (GPLv3)
 */

class Auguria_Core_Model_Customer_Attribute_Source_Group extends Mage_Eav_Model_Entity_Attribute_Source_Table
{
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = Mage::getResourceModel('customer/group_collection')
            //->setRealGroupsFilter() GET NOT LOGGED IN GROUP
            ->load()
            ->toOptionArray()
            ;
        }
        return $this->_options;
    }
}