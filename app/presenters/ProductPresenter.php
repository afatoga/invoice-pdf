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
          $this->flashMessage('Tato stránka je dostupná pouze pro správce aplikace.');
          //$this->redirect('Sign:in'); 
        }
      }

      public function renderDetail(int $id = 0): void
      { 
        echo $id;
        echo 'ahoj';
        //var_dump($_GET['orderId']);
      }

}
