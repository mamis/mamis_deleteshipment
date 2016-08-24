<?php
/**
*  Mamis.IT
*
*  NOTICE OF LICENSE
*
*  This source file is subject to the EULA
*  that is available through the world-wide-web at this URL:
*  http://www.mamis.com.au/licencing
*
*  @category   Mamis
*  @copyright  Copyright (c) 2015 by Mamis.IT Pty Ltd (http://www.mamis.com.au)
*  @author     Matthew Muscat <matthew@mamis.com.au>
*  @license    http://www.mamis.com.au/licencing
*/

class Mamis_DeleteShipment_Adminhtml_Mamisdeleteshipment_ShipmentController extends Mage_Adminhtml_Controller_Action
{
    protected function deleteAction()
    {
        $shipmentId = $this->getRequest()->getParam('shipment_id');

        // Load the shipment
        $shipment = Mage::getModel('sales/order_shipment')
            ->load($shipmentId);

        $order = $shipment->getOrder();

        $shipmentItems = $shipment->getItemsCollection();

        try {
            foreach ($shipmentItems as $shipmentItem)
            {
                // Reset the item shipment qty for items part of the shipment
                $orderItem = $order->getItemById($shipmentItem->getOrderItemId());
                $orderItem->setQtyShipped($orderItem->getQtyShipped() - $shipmentItem->getQty());
                $orderItem->save();
            }

            // Delete the shipment
            $shipment->delete();

            // Reset the order status to partially shipped
            $order->setState(
                Mage_Sales_Model_Order::STATE_PROCESSING,
                true,
                'A shipment record was deleted'
            );
            $order->save();

            Mage::getSingleton('adminhtml/session')->addSuccess("Shipment record deleted successfully");
        }
        catch (Exception $e)
        {
            Mage::getSingleton('adminhtml/session')->addError("An error occured while attempting to deelete the shipment record");
        }

        Mage::app()->getResponse()->setRedirect(
            Mage::helper('adminhtml')->getUrl(
                "adminhtml/sales_order/view",
                array('order_id' => $order->getId())
            )
        );
    }

    /**
     * Acl check for admin
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/shipment/actions/delete');
    }
}