<?php
/**
 * @category   Auguria
 * @package    Auguria_Core
 * @author     Auguria
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3 (GPLv3)
 */

abstract class Auguria_Core_Model_System_Config_Source_Config
{

    const CONFIG_OPTIONS_PATH = '';

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $nodes = Mage::getConfig()->getNode(static::CONFIG_OPTIONS_PATH);
        $translator = (string)$nodes->getAttribute('translator'); // used to translate label

        $options = array();

        foreach($nodes as $_node){
            foreach ($_node as $_option){
                $options[] = array('value' => (string)$_option->value, 'label' => Mage::helper($translator)->__((string)$_option->label));
            }
        }

        return $options;
    }

}