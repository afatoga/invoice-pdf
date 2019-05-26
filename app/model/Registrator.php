<?php

namespace App\Model;

use Nette;
use Nette\Security as NS;

class Registrator
{
	private $database;

	function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
	}

	function register(array $credentials): void
	{
        list($username, $password) = $credentials;
        //check if not registered
        $row = $this->database->query('SELECT Email
        FROM vm_user
        WHERE Email = ?', $username);
        $row = $row->fetch();
		if ($row) {
			throw new Exception('Uživatel s tímto emailem je již registrován.');
        }

        else {
            $nettePasswords = new NS\Passwords;
            $hashedPassword = $nettePasswords->hash($password);
            $row = $this->database->query('INSERT INTO vm_user (Email, HashedPassword) VALUES (?, ?)', $username, $hashedPassword);
        }
	}
}