<?php

namespace App\Model;

use Nette;

class OrderController
{
	private $database;

	function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
    }

    public function isCustomersOrder(int $customerId, int $orderId): bool 
    {
        $sql = $this->database->query('SELECT vm_order.Id, vm_order.CustomerId 
                          FROM vm_order
                          WHERE vm_order.Id = ?
                          AND vm_order.CustomerId = ?', $orderId, $customerId);
        if ($sql->getRowCount()>0) {
            return true;
        }
        return false;
    }

    public function isProductItemPresentInOrder(int $orderId, int $productId): bool 
    {
        $sql = $this->database->query('SELECT OrderId, ProductId 
                          FROM vm_orderDetails
                          WHERE OrderId = ?
                          AND ProductId = ?', $orderId, $productId);
        if ($sql->getRowCount()>0) {
            return true;
        }
        return false;
    }

    public function getOrder(int $orderId): ?array
    {
        $sql = $this->database->query('SELECT vm_order.Id, vm_order.InsertTime, vm_order.CustomerId, 
                                vm_user.Email
                                FROM vm_order
                                LEFT OUTER JOIN vm_user ON vm_order.CustomerId = vm_user.Id
                                WHERE vm_order.Id = ?', $orderId);
        if ($sql->getRowCount()>0) {
            $order = $sql->fetchAll();
            return $order;
        }
        return null;
    }

    public function getOrderDetails(int $orderId): ?array
    {
        $sql = $this->database->query('SELECT vm_order.Id, vm_orderDetails.ProductId, vm_orderDetails.Quantity, vm_orderDetails.Price,
                                vm_product.Title, vm_product.Description
                                FROM vm_order
                                LEFT OUTER JOIN vm_orderDetails ON vm_order.Id = vm_orderDetails.OrderId
                                LEFT OUTER JOIN vm_product ON vm_orderDetails.ProductId = vm_product.Id
                                WHERE vm_order.Id = ?', $orderId);
        if ($sql->getRowCount()>0) {
            $orderDetails = $sql->fetchAll();
            return $orderDetails;
        }
        return null;
    }
}