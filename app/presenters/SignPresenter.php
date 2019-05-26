<?php

declare(strict_types=1);

//require 'Nette/loader.php';

//require 'MyAuthenticator.php';

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use App\Model\Authenticator;
use App\Model\Registrator;
use Nette\Security\User;


final class SignPresenter extends Nette\Application\UI\Presenter
{
    private $authenticator;
    private $database;

    public function __construct(Authenticator $authenticator, Nette\Database\Context $database)
    {
        $this->authenticator = $authenticator;
        $this->database = $database;
    }

    public function renderIn(): void 
    {    
        $user = $this->getUser();
        echo $user->isLoggedIn() ? 'ano' : 'ne';
    }

    public function renderUp(): void 
    {    
        echo 'hello';
    }


    protected function createComponentSignUpForm(): Form
    {
        $form = new Form;
        $form->addEmail('email', 'E-mail:')
            ->setRequired('Prosím vyplňte svůj email.');

        $form->addPassword('password', 'Heslo:')
            ->setRequired('Prosím vyplňte své heslo.');

        $form->addPassword('passwordAgain', 'Heslo (vyplňte znovu):')
            ->setRequired('Prosím vyplňte své heslo znovu.');

        $form->addSubmit('send', 'Registrovat');

        $form->onSuccess[] = [$this, 'signUpFormSucceeded'];
        return $form;
    }

    public function signUpFormSucceeded(Form $form, \stdClass $values): void
    {   
        $credentials = [];
        if ($values->password == $values->passwordAgain) {
        $registrator = new Registrator ($this->database);
        $credentials = [$values->email, $values->password];
        $registrator->register($credentials);

        //login
        $this->getUser()->login($values->email, $values->password);
        $this->redirect('Product:index');
        }
        
    }

    protected function createComponentSignInForm(): Form
    {
        $form = new Form;
        $form->addText('email', 'E-mail:')
            ->setRequired('Prosím vyplňte svůj e-mail.');

        $form->addPassword('password', 'Heslo:')
            ->setRequired('Prosím vyplňte své heslo.');

        $form->addSubmit('send', 'Přihlásit');

        $form->onSuccess[] = [$this, 'signInFormSucceeded'];
        return $form;
    }

    public function signInFormSucceeded(Form $form, \stdClass $values): void
    {
        try {
            $this->getUser()->login($values->email, $values->password);
            $this->redirect('Product:index');

        } catch (Nette\Security\AuthenticationException $e) {
            $form->addError('Nesprávné přihlašovací jméno nebo heslo.');
           
        }
    }

}