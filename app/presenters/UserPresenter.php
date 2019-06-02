<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;


final class UserPresenter extends Nette\Application\UI\Presenter
{
      /** @var Nette\Database\Context */
      private $database;

      public function __construct(Nette\Database\Context $database)
      {
          $this->database = $database;
      }

      public function renderDetail(): void
      { 
        $user = $this->getUser();
        if ($user->isLoggedIn()) {
            //vsechny informace o uzivateli
            $sql = $this->database->query('SELECT Id, Email, `Name`, Surname, `Address`, City, Zip, Country 
            FROM vm_user
            WHERE Id = ?', $user->getId());
            if ($sql->getRowCount()>0) {
                $userDetails = $sql->fetch();

                  $this['editUserForm']->getComponent('customerId')
                                          ->setValue($user->getId());
                  //prirazeni hodnot existujiciho produktu
                  $this['editUserForm']->setDefaults([
                                      'email' => $userDetails->Email,
                                      'name' => $userDetails->Name,
                                      'surname' => $userDetails->Surname,
                                      'address' => $userDetails->Address,
                                      'city' => $userDetails->City,
                                      'zip' => $userDetails->Zip
                  ]);

            } else {
                $this->flashMessage('Uživatel nenalezen', 'alert-warning');
            }
        }
        else {
          $this->flashMessage('Tato stránka je dostupná pouze pro registrované', 'alert-warning');
          $this->redirect('Homepage:default'); 
        }
      }

      protected function createComponentEditUserForm(): Form
    {   
        $form = new Form;

        $form->addHidden('customerId');

        $form->addEmail('email', 'E-mail:')
             ->setRequired('Prosím vyplňte e-mail.');

        $form->addText('name', 'Jméno:')
             ->setRequired('Prosím vyplňte křestní jméno.')
             ->addRule(Form::MAX_LENGTH, 'Jmeno nesmí být delší než %d znaků', 60);
             

        $form->addText('surname', 'Příjmení:')
             ->setRequired('Prosím vyplňte příjmení.')
             ->addRule(Form::MAX_LENGTH, 'Příjmení nesmí být delší než %d znaků', 60);

        $form->addText('address', 'Ulice a č. p.:')
             ->setRequired('Prosím vyplňte ulici a č.p.')
             ->addRule(Form::PATTERN, 'Adresa nevalidní', '^(.*[^0-9]+) (([1-9][0-9]*)/)?([1-9][0-9]*[a-cA-C]?)$');

        $form->addText('city', 'Město:')
             ->setRequired('Prosím vyplňte město.')
             ->addRule(Form::MAX_LENGTH, 'Název města nesmí být delší než %d znaků', 60);

        $form->addText('zip', 'PSČ:')
             ->setHtmlType('number')
             ->addRule(Form::PATTERN, 'PSČ musí mít 5 číslic', '([0-9]\s*){5}')
             ->setRequired('Prosím vyplňte PSČ.');

        $form->addSelect('country', 'Země:', ['Czechia'=>'Česká republika',
        'Germany'=>'Německo'])
             ->setPrompt('Zvolte zemi')
             ->setRequired('Vyberte prosím zemi.');

        $form->addSubmit('send', 'Uložit');

        $form->onSuccess[] = [$this, 'editUser'];
        return $form;
    }

    public function editUser(Form $form, \stdClass $values): void
    {
      $user = $this->getUser();
        
        if($user->getId() == $values->customerId) {

          $sql = $this->database->query('UPDATE vm_user SET Email = ?, `Name` = ?, Surname = ?, `Address`= ?, City = ?, Zip = ?, Country = ? 
                                         WHERE Id = ?',
                                         $values->email, $values->name, $values->surname,$values->address,$values->city,$values->zip,$values->country, $values->customerId);
                              
            if($sql->getRowCount()>0) {
               $this->flashMessage('Úspěšně změněno.', 'alert-success');  
            } else {
              $this->flashMessage('Nelze změnit.', 'alert-danger');
            }
           
      } else {
        throw new Nette\Application\BadRequestException('Chyba', 403);
      }

    }
}