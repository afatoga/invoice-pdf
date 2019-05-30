<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;


final class ProductPresenter extends Nette\Application\UI\Presenter
{
      /** @var Nette\Database\Context */
      private $database;

      public function __construct(Nette\Database\Context $database)
      {
          $this->database = $database;
      }

      public function renderIndex(): void
      { 
        $user = $this->getUser();
        if ($user->isLoggedIn() && $user->isInRole('admin')) {
        //vsechny produkty
        $productList = $this->database->query('SELECT vm_product.Id, vm_product.Title, vm_product.Description, vm_product.Price 
        FROM vm_product');
        $this->template->products = $productList;
        }
        else {
          $this->flashMessage('Tato stránka je dostupná pouze pro správce aplikace.', 'alert-warning');
          //$this->redirect('Sign:in'); 
        }
      }

      public function renderDetail(int $id = 0): void
      { 
        echo $id;
        echo 'ahoj';
        //var_dump($_GET['orderId']);
      }

      public function actionCreate(): void
      {

      }

      protected function createComponentCreateProductForm(): Form
      {
          $form = new Form;
          $form->addText('title', 'Název:')
              ->setRequired('Zadejte název.');
  
          $form->addText('description', 'Popis:')
              ->setRequired('Zadejte popis.');

          $form->addText('price', 'Cena:')
              ->setRequired('Zadejte cenu.')
              ->setHtmlType('number')
              ->addRule(Form::INTEGER, 'Cena musí být číslo.')
              ->addRule(Form::RANGE, 'Cena musí být v rozmezí 0 až 100 000.', [0, 100000]);    
  
          $form->addSubmit('send', 'Uložit');
  
          $form->onSuccess[] = [$this, 'createProductFormSucceeded'];
          return $form;
      }
  
      public function createProductFormSucceeded(Form $form, \stdClass $values): void
      {
          try {
              $this->getUser()->login($values->email, $values->password);
              $this->database->query('INSERT INTO vm_product (Title, `Description`, Price) VALUES (?, ?, ?)', $values->Title, $values->Description, $values->Price);
              $this->redirect('Product:index');
  
          } catch (Nette\Application\BadRequestException $e) {
            $form->addError($e->getMessage());
            }
      }

}
