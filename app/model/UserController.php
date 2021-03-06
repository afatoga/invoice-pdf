<?php

namespace App\Model;

use Nette;

class UserController
{
	private $database;

	function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
    }

    public function getCustomerIdByEmail(string $email): ?int
    {
        $sql = $this->database->query('SELECT vm_user.Id 
                          FROM vm_user
                          WHERE Email = ?', $email);

        if ($sql->getRowCount()>0) {
            $customer = $sql->fetch();
            return (int)$customer['Id'];
        } else {
            return null;
        }
    }

    public function getUserDetails(int $id): ?object
    {
        $sql = $this->database->query('SELECT vm_user.* 
                          FROM vm_user
                          WHERE Id = ?', $id);

        if ($sql->getRowCount()>0) {
            $userDetails = $sql->fetch();
            return $userDetails;
        } else {
            return null;
        }
    }
}