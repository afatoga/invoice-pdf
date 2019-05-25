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
		list($username, $password) = $credentials;
		$row = $this->database->table('vm_user')
			->where('Email', $username)->fetch();

		if (!$row) {
			throw new NS\AuthenticationException('User not found.');
		}
        //var_dump($row);
        $nettePasswords = new NS\Passwords;
		if ($password != $row->HashedPassword) { //!$nettePasswords->verify($password, $row->HashedPassword)
			throw new NS\AuthenticationException('Invalid password.');
		}

		return new NS\Identity($row->id, $row->role);
	}
}

    /*
    public function authenticate(array $credentials)
    {
        $username = $credentials[self::USERNAME];
        $password = sha1($credentials[self::PASSWORD] . $credentials[self::USERNAME]);

        // přečteme záznam o uživateli z databáze
        $row = dibi::fetch('SELECT realname, password FROM users WHERE login=%s', $username);

        if (!$row) { // uživatel nenalezen?
            throw new AuthenticationException("User '$username' not found.", self::IDENTITY_NOT_FOUND);
        }

        if ($row->password !== $password) { // hesla se neshodují?
            throw new AuthenticationException("Invalid password.", self::INVALID_CREDENTIAL);
        }

        return new Identity($row->realname); // vrátíme identitu
    }
    */