<?php
/**
 * @category   Auguria
 * @package    Auguria_Core
 * @author     Auguria
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3 (GPLv3)
 */

abstract class Auguria_Core_Model_Attribute_Backend_Image_Abstract extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Save uploaded file
     *
     * @param Varien_Object $object
     */
    public function afterSave($object)
    {
        $value = $object->getData($this->getAttribute()->getName());

        if (is_array($value) && !empty($value['delete'])) {
            $object->setData($this->getAttribute()->getName(), '');
            $this->getAttribute()->getEntity()
            ->saveAttribute($object, $this->getAttribute()->getName());
            return;
        }

        try {
            $uploader = new Mage_Core_Model_File_Uploader($this->getAttribute()->getName());
            $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
            $uploader->setAllowRenameFiles(true);
            $result = $uploader->save($this->_getPath());

            $object->setData($this->getAttribute()->getName(), $this->_getPrefix() . $result['file']);
            $this->getAttribute()->getEntity()->saveAttribute($object, $this->getAttribute()->getName());
        } catch (Exception $e) {
            if ($e->getCode() != Mage_Core_Model_File_Uploader::TMP_NAME_EMPTY) {
                Mage::logException($e);
            }
            return;
        }
    }

    /**
     * Get path to save file
     *
     * @return string
     */
    abstract protected function _getPath();

    /**
     * Get prefix when save path file in attribute value
     *
     * @return string
     */
    protected function _getPrefix()
    {
        return;
    }
}