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
        $row = $this->database->query('SELECT Email, HashedPassword
        FROM vm_user
        WHERE Email = ?', $username);
        $row = $row->fetch();
		if ($row && $row['HashedPassword']!==null) {
            throw new Nette\Application\BadRequestException('Uživatel s tímto emailem je již registrován.');
        }

        else {
            if (isset($password)) {

                $nettePasswords = new NS\Passwords;
                $hashedPassword = $nettePasswords->hash($password);

                
                if(is_null($row['HashedPassword'])) {
                    //registruji uzivatele, ktery ma objednavku, ale nebyl predtim registrovan
                    $row = $this->database->query('UPDATE vm_user
                                                   SET HashedPassword = ? 
                                                   WHERE Email = ?', 
                                                   $hashedPassword, $username);
                } else {
                    $row = $this->database->query('INSERT INTO vm_user (Email, HashedPassword) VALUES (?, ?)', 
                    $username, $hashedPassword);
                }
           
            
            } else {
                //admin registruje uzivatele bez hesla
                $row = $this->database->query('INSERT INTO vm_user (Email) VALUES (?)', 
                $username);
            }
        }
	}
}