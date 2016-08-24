<?php

class Mamis_DeleteShipment_Model_Observer
{
    public function addDeleteButton(Varien_Event_Observer $observer)
    {
        $block = $observer->getBlock();

        if ($block instanceof Mage_Adminhtml_Block_Sales_Order_Shipment_View
            && Mage::getSingleton('admin/session')->isAllowed('sales/shipment/actions/delete'))
        {
            $message = Mage::helper('mamis_deleteshipment')->__('Are you sure you want to delete this shipment?');
            $block->addButton('delete', array(
                'label'     => Mage::helper('mamis_deleteshipment')->__('Delete Shipment'),
                'onclick'   => "confirmSetLocation('{$message}', '{$this->getDeleteUrl($block)}')",
                'class'     => 'delete'
            ));
        }
    }

    public function getDeleteUrl($block)
    {
        return $block->getUrl(
            'adminhtml/mamisdeleteshipment_shipment/delete',
            array('shipment_id' => $block->getShipment()->getId())
        );
    }
}