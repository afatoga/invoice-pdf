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
        $row = $this->database->query('SELECT vm_user.Email, vm_user.HashedPassword FROM vm_user WHERE Email = ?', $username);
        $row = $row->fetch();
        //existuje zaznam od uzivatele, ktery se sam registroval
		if ($row && $row['HashedPassword'] !== null) {
            throw new Nette\Application\BadRequestException('Uživatel s tímto emailem je již registrován.');
        }
        //existuje zaznam ale uzivatele pridal admin
        else if ($row && $row['HashedPassword'] == null && isset($password)) {
            $nettePasswords = new NS\Passwords;
            $hashedPassword = $nettePasswords->hash($password);
            
            $row = $this->database->query('UPDATE vm_user
                                           SET HashedPassword = ? 
                                           WHERE Email = ?', 
                                           $hashedPassword, $username);
        }
        //neexistuje zaznam, klasicka registrace
        else if (!$row && isset($password)) {

            $nettePasswords = new NS\Passwords;
            $hashedPassword = $nettePasswords->hash($password);    
            $row = $this->database->query('INSERT INTO vm_user (Email, HashedPassword) VALUES (?, ?)', 
                    $username, $hashedPassword);
                
        } 
        //neexistuje zaznam, uzivatel vlozen adminem bez hesla
        else if (!$row) {
                //admin registruje uzivatele bez hesla
                $row = $this->database->query('INSERT INTO vm_user (Email) VALUES (?)', 
                $username);
        }
	}
}