<?php

namespace App\Model;

use Nette;
use Nette\Security as NS;

class Authenticator implements NS\IAuthenticator
{
	public $database;

	function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
	}

	function authenticate(array $credentials): NS\IIdentity
	{
        list($email, $password) = $credentials;
        $row = $this->database->query('SELECT Id, Email, HashedPassword, RoleId
        FROM vm_user
        WHERE Email = ?', $email);
        $row = $row->fetch();
		if (!$row) {
			throw new NS\AuthenticationException('Uživatel nenalezen.');
        }
        
        //uzivatel se nezaregistroval, ucet vytvoren adminem
        if (!is_null($row['HashedPassword'])) {

            $nettePasswords = new NS\Passwords;
            if (!$nettePasswords->verify($password, $row['HashedPassword'])) 
            {
                throw new NS\AuthenticationException('Invalid password.');
            }

        } else {
            throw new NS\AuthenticationException('Nelze se přihlásit, kontaktujte správce.');
        }
        
        
        $role = '';
        if ($row['RoleId'] == 1) {
            $role = 'admin'; 
        }
        else {
            $role = 'member';
        }

		return new NS\Identity($row['Id'], $role, ['email' => $row['Email']]);
	}
}