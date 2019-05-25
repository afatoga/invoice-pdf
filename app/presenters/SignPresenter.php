<?php

declare(strict_types=1);

//require 'Nette/loader.php';

//require 'MyAuthenticator.php';

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use App\Model\Authenticator;
use Nette\Security\User;


final class SignPresenter extends Nette\Application\UI\Presenter
{
    private $authenticator;

    public function __construct(Authenticator $authenticator)
    {
        $this->authenticator = $authenticator;
    }

    public function renderIn(): void 
    {    
        $user = $this->getUser();
        echo $user->isLoggedIn() ? 'ano' : 'ne';
    }

    protected function createComponentSignInForm(): Form
    {
        $form = new Form;
        $form->addText('username', 'Uživatelské jméno:')
            ->setRequired('Prosím vyplňte své uživatelské jméno.');

        $form->addPassword('password', 'Heslo:')
            ->setRequired('Prosím vyplňte své heslo.');

        $form->addSubmit('send', 'Přihlásit');

        $form->onSuccess[] = [$this, 'signInFormSucceeded'];
        return $form;
    }

    public function signInFormSucceeded(Form $form, \stdClass $values): void
    {
        try {
            $this->getUser()->login($values->username, $values->password);
            //$this->redirect('Homepage:');

        } catch (Nette\Security\AuthenticationException $e) {
            $form->addError('Nesprávné přihlašovací jméno nebo heslo.');
           
        }
    }

}