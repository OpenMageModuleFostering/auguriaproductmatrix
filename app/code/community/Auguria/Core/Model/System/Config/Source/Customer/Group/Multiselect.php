<?php
/**
 * @category   Auguria
 * @package    Auguria_Core
 * @author     Auguria
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3 (GPLv3)
 */

class Auguria_Core_Model_System_Config_Source_Customer_Group_Multiselect
{

    /**
     * Customer groups options array
     *
     * @var null|array
     */
    protected $_options;

    /**
     * Retrieve customer groups as array
     *
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = Mage::getResourceModel('customer/group_collection')
                //->setRealGroupsFilter() Get the NOT LOGGED IN GROUP TOO
                ->loadData()->toOptionArray();
        }
        return $this->_options;
    }

}