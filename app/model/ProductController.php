<?php

namespace App\Model;

use Nette;

class ProductController
{
	private $database;

	function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
    }

    public function getProductList(): array
    {
        $sql = $this->database->query('SELECT vm_product.Id, vm_product.Title 
                          FROM vm_product');
        return $sql->fetchAll();
    }
}