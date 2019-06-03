<?php

declare(strict_types=1);

//require 'Nette/loader.php';

//require 'MyAuthenticator.php';

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use App\Model\Authenticator;
use App\Model\Registrator;


final class SignPresenter extends Nette\Application\UI\Presenter
{
    private $authenticator;
    private $database;

    public function __construct(Authenticator $authenticator, Nette\Database\Context $database)
    {
        $this->authenticator = $authenticator;
        $this->database = $database;
    }

    public function renderUp(): void 
    {    
        $user = $this->getUser();
        if ($user->isLoggedIn()) {
            $this->flashMessage('Jste již přihlášen.', 'alert-warning');
            $this->redirect('Homepage:default');
        }
    }

    public function renderIn(): void 
    {    
        $user = $this->getUser();
        if ($user->isLoggedIn()) {
            $this->flashMessage('Jste již přihlášen.', 'alert-warning');
            $this->redirect('Homepage:default');
        }
    }

    public function actionOut(): void 
    {   
        $user = $this->getUser();
        if ($user->isLoggedIn()) {
            $this->getUser()->logout();
            $this->flashMessage('Byl jste odhlášen.', 'alert-warning');
            $this->redirect('Homepage:default');
            }
        else {
            $this->flashMessage('Nejste přihlášen.', 'alert-warning');
        }
    }


    protected function createComponentSignUpForm(): Form
    {
        $form = new Form;
        $form->addProtection();
        $form->addEmail('email', 'E-mail:')
            ->setRequired('Prosím vyplňte svůj email.');

        $form->addPassword('password', 'Heslo:')
            ->setRequired('Prosím vyplňte své heslo.')
            //->addRule(Form::MIN_LENGTH, 'Heslo nesmí být kratší než %d znaků', 6)
            ->addRule(Form::PATTERN, 'Heslo nevalidní, musí mít 8 znaků, nejméně 1 číslo a 1 písmeno', '^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$');

        $form->addPassword('passwordVerify', 'Heslo (vyplňte znovu):')
            ->setRequired('Prosím vyplňte své heslo znovu.')
            ->addRule(Form::EQUAL, 'Hesla se neshodují.', $form['password']);

        $form->addSubmit('send', 'Registrovat');

        $form->onSuccess[] = [$this, 'signUpFormSucceeded'];
        return $form;
    }

    public function signUpFormSucceeded(Form $form, \stdClass $values): void
    {   
        $credentials = [];
        if ($values->password == $values->passwordVerify) {

            try {
                $registrator = new Registrator ($this->database);
                $credentials = [$values->email, $values->password];
                $registrator->register($credentials);
        
                //login
                $this->getUser()->login($values->email, $values->password);
                $this->flashMessage('Děkujeme za registraci, nyní jste přihlášen.', 'alert-success');
                $this->redirect('Homepage:default');
    
            } catch (Nette\Application\BadRequestException $e) {
                $form->addError($e->getMessage());
            }

       
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
            $this->flashMessage('Jste přihlášen.', 'alert-success');
            $this->redirect('Homepage:default');

        } catch (Nette\Security\AuthenticationException $e) {
            $form->addError('Nesprávné přihlašovací jméno nebo heslo.');
           
        }
    }

   

}