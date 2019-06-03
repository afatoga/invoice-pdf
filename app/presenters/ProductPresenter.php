<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;


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
        if ($user->isInRole('admin')) {
        //vsechny produkty
        $productList = $this->database->query('SELECT vm_product.Id, vm_product.Title, vm_product.Description
        FROM vm_product');
        $this->template->products = $productList;
        }
        else {
          $this->flashMessage('Tato stránka je dostupná pouze pro správce aplikace.', 'alert-warning');
          $this->redirect('Homepage:default'); 
        }
      }

      public function actionEdit(?int $id): void
      { 
        $user = $this->getUser();
        if ($user->isInRole('admin')) {
        //nacteni produktu, pokud existuje id
              if(isset($id)) {

              $sql = $this->database->query('SELECT vm_product.Id, vm_product.Title, vm_product.Description
              FROM vm_product
              WHERE Id = ?', $id);
              $product = $sql->fetch();
                  if($product) {
                            //nastavim id, ktere nelze zmenit
                            $this['editProductForm']->getComponent('productId')
                                              ->setValue($id);
                            //prirazeni hodnot existujiciho produktu
                            $this['editProductForm']->setDefaults([
                                          'title' => $product->Title,
                                          'description' => $product->Description
                            ]);
                  }
                  
              }
              
        }
        else {
          $this->flashMessage('Tato stránka je dostupná pouze pro správce aplikace.', 'alert-warning');
          $this->redirect('Homepage:default'); 
        }

      }

      protected function createComponentEditProductForm(): Form
      {
          $form = new Form;
          $form->addText('title', 'Název:')
               ->setRequired('Zadejte název.');
  
          $form->addTextArea('description', 'Popis:')
                ->setRequired('Zadejte popis.')
                ->setHtmlAttribute('autocomplete', 'off');
  
          $form->addHidden('productId');

          $form->addSubmit('send', 'Uložit');
  
          $form->onSuccess[] = [$this, 'editProductFormSucceeded'];
          return $form;
      }
  
      public function editProductFormSucceeded(Form $form, \stdClass $values): void
      {   
          if (!empty($values->productId)) {
            $sql = $this->database->query('UPDATE vm_product 
                                           SET `Title` = ?, `Description` = ? 
                                           WHERE `Id` = ?', 
                                           $values->title, $values->description, $values->productId);
          } else {
            $sql = $this->database->query('INSERT INTO vm_product (Title, `Description`) VALUES (?, ?)', $values->title, $values->description);
          }

          if ($sql->getRowCount()>0) {
            $this->flashMessage('Produkt se uložil', 'alert-success');
            
          } else {
            $this->flashMessage('Produkt se neuložil', 'alert-danger');
          }
          $this->redirect('Product:index');
      }

}
