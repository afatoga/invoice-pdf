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

    public function isCustomersOrder (int $customerId, int $orderId): bool 
    {
        $sql = $this->database->query('SELECT vm_order.Id, vm_order.CustomerId 
                          FROM vm_order
                          WHERE vm_order.Id = ?
                          AND vm_order.CustomerId = ?', $orderId, $customerId);
        if (!empty($sql->fetch())) {
            return true;
        }
        return false;
    }
}