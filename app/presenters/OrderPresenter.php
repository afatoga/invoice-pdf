<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use App\Model\OrderController;
use App\Model\ProductController;
use App\Model\UserController;
use App\Model\Registrator;
use App\Model\PDFOutputController;
use \Joseki\Application\Responses\PdfResponse;


final class OrderPresenter extends Nette\Application\UI\Presenter
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
        if ($user->isLoggedIn()) {
          if ($user->isInRole('admin')) {
            //vypisuju vsechny objednavky
            $orderList = $this->database->query('SELECT vm_order.Id, vm_order.InsertTime, vm_order.CustomerId, 
                          vm_order.StatusId, vm_user.Email, vm_orderStatus.Title AS `Status` 
                          FROM vm_order 
                          LEFT OUTER JOIN vm_orderStatus ON vm_order.StatusId = vm_orderStatus.Id 
                          LEFT OUTER JOIN vm_user ON vm_order.CustomerId = vm_user.Id');
            $this->template->orderList = $orderList;
          }
          else {
            //vypisuju objednavky konkretniho uzivatele
            $customerId = $user->getId();
            $orderList = $this->database->query('SELECT vm_order.Id, vm_order.InsertTime, vm_order.CustomerId, 
                         vm_order.StatusId, vm_user.Email, vm_orderStatus.Title AS `Status`
                         FROM vm_order 
                         LEFT OUTER JOIN vm_orderStatus ON vm_order.StatusId = vm_orderStatus.Id 
                         LEFT OUTER JOIN vm_user ON vm_order.CustomerId = vm_user.Id
                         WHERE vm_order.CustomerId = ?', $customerId);
            $this->template->orderList = $orderList->fetchAll();
            $this->template->customerId = $customerId;
          }
        }
        else {
          $this->redirect('Sign:in');
        }   
        
      }

      public function renderDetail(int $id): void
      { 
        $user = $this->getUser();
        $orderController = new OrderController($this->database);
        
        if($orderController->isCustomersOrder($user->getId(), $id) || $user->isInRole('admin')) 
        {
            //umoznit adminovi pridavat polozky
            $this->template->orderId = $id;
            if ($user->isInRole('admin')) {
              $this['addProductItemForm']->getComponent('orderId')
                                        ->setValue($id);
            } 

            //nacteni polozek
            $sql = $this->database->query('SELECT vm_order.Id, vm_order.InsertTime, 
                            vm_order.StatusId, vm_orderStatus.Title AS `StatusTitle`, vm_orderDetails.Id AS `OrderItemId`, vm_orderDetails.ProductId, vm_orderDetails.Quantity, vm_orderDetails.Price,
                            vm_product.Title, vm_product.Description, vm_product.Title AS `ProductTitle` 
                            FROM vm_order 
                            LEFT OUTER JOIN vm_orderStatus ON vm_order.StatusId = vm_orderStatus.Id 
                            INNER JOIN vm_orderDetails ON vm_order.Id = vm_orderDetails.OrderId
                            LEFT OUTER JOIN vm_product ON vm_orderDetails.ProductId = vm_product.Id
                            WHERE vm_order.Id = ?', $id);

              if($sql->getRowCount()>0) {
                $orderDetails = $sql->fetchAll();
                //spocitani celkovy ceny objednavky
                $orderTotalPrice = 0;
                foreach ($orderDetails as $detail) {
                  $orderTotalPrice += $detail['Price']*$detail['Quantity'];
                }

                $this->template->order = $orderDetails;
                $this->template->orderTotalPrice = $orderTotalPrice;

              
              } else {
              $this->flashMessage('Položky objednávky neexistují.', 'alert-warning');
              }            
          }
          else {
            throw new Nette\Application\BadRequestException('Objednávka pro vás není dostupná', 403);
          }
    }

    public function actionCancel(int $id): void
    {
      $user = $this->getUser();
        $orderController = new OrderController($this->database);
        
        if($orderController->isCustomersOrder($user->getId(), $id) || $user->isInRole('admin')) {

           $sql = $this->database->query('UPDATE vm_order 
                                         SET vm_order.StatusId = 3
                                         WHERE vm_order.Id = ?', $id);
           $this->setView('index');
                              
            if($sql->getRowCount()>0) {
               $this->flashMessage('Úspěšně stornováno.', 'alert-success');  
            } else {
              $this->flashMessage('Nelze stornovat.', 'alert-danger');
            }
      } else {
        throw new Nette\Application\BadRequestException('Objednávka pro vás není dostupná', 403);
      }

    }

    public function actionRestore(int $id): void
    {
      $user = $this->getUser();
        $orderController = new OrderController($this->database);
        
        if($user->isInRole('admin')) {

           $sql = $this->database->query('UPDATE vm_order 
                                         SET vm_order.StatusId = 1
                                         WHERE vm_order.Id = ?', $id);
           $this->setView('index');
                              
            if($sql->getRowCount()>0) {
               $this->flashMessage('Úspěšně obnoveno.', 'alert-success');  
            } else {
              $this->flashMessage('Nelze obnovit.', 'alert-danger');
            }
      } else {
        throw new Nette\Application\BadRequestException('Objednávka pro vás není dostupná', 403);
      }

    }

    public function actionSetPaid(int $id): void
    {
      $user = $this->getUser();
        $orderController = new OrderController($this->database);
        
        if($user->isInRole('admin')) {

           $sql = $this->database->query('UPDATE vm_order 
                                         SET vm_order.StatusId = 2
                                         WHERE vm_order.Id = ?', $id);
           $this->setView('index');
                              
            if($sql->getRowCount()>0) {
               $this->flashMessage('Označeno jako zaplacené.', 'alert-success');  
            } else {
              $this->flashMessage('Nelze označit.', 'alert-danger');
            }
      } else {
        throw new Nette\Application\BadRequestException('Objednávka pro vás není dostupná', 403);
      }

    }

    public function actionRemoveProductItem(int $orderId, int $itemId): void
    {
      $user = $this->getUser();
        //$orderController = new OrderController($this->database);
        
        if($user->isInRole('admin')) {

           $sql = $this->database->query('DELETE 
                                         FROM vm_orderDetails
                                         WHERE vm_orderDetails.Id = ?', $itemId);
            if($sql->getRowCount()>0) {
               $this->flashMessage('Úspěšně odstraněno.', 'alert-success');  
            } else {
              $this->flashMessage('Nelze odstranit.', 'alert-danger');
            }
           $this->redirect('Order:detail', $orderId);
                              
      } else {
        throw new Nette\Application\BadRequestException('Objednávka pro vás není dostupná', 403);
      }

    }

    protected function createComponentAddOrderForm(): Form
    {   
        $form = new Form;

        $form->addEmail('email', 'E-mail:')
            ->setRequired('Prosím vyplňte e-mail zákazníka.');

        $form->addSubmit('send', 'Vytvořit');

        $form->onSuccess[] = [$this, 'addOrder'];
        return $form;
    }

    public function addOrder(Form $form, \stdClass $values): void
    {
      $user = $this->getUser();
        
        if($user->isInRole('admin')) {

          $userController = new UserController($this->database);
          $customerId = $userController->getCustomerIdByEmail($values->email);

          //zakaznik neni registrovan
          if (is_null($customerId)) {
            $registrator = new Registrator ($this->database);
            $registrator->register([$values->email, null]);
            $customerId = $userController->getCustomerIdByEmail($values->email);
          }

          $sql = $this->database->query('INSERT INTO vm_order (CustomerId) 
                                          VALUES (?)', $customerId);
           $this->setView('index');
                              
            if($sql->getRowCount()>0) {
               $this->flashMessage('Úspěšně vloženo.', 'alert-success');  
            } else {
              $this->flashMessage('Nelze vložit.', 'alert-danger');
            }
           
      } else {
        throw new Nette\Application\BadRequestException('Nemáte práva administrátora.', 403);
      }

    }

    protected function createComponentAddProductItemForm(): Form
    {   
        $form = new Form;

        $form->addHidden('orderId');

        $productController = new ProductController($this->database);
        $productList = $productController->getProductList();
        $products = [];
        foreach ($productList as $product) {
          $products[$product['Id']] = $product['Title'];
        }

        $form->addSelect('productId', 'Produkt', $products)
              ->setPrompt('Zvolte produkt')
              ->setRequired('Zvolte produkt.');

        $form->addText('quantity', 'Počet:')
             ->setRequired('Zadejte počet.')
             ->setHtmlType('number')
             ->addRule(Form::INTEGER, 'Počet musí být číslo.')
             ->addRule(Form::RANGE, 'Cena musí být v rozmezí 1 až 1000.', [1, 1000]);
             
        $form->addText('price', 'Cena:')
             ->setRequired('Zadejte cenu.')
             ->setHtmlType('number')
             ->addRule(Form::INTEGER, 'Cena musí být číslo.')
             ->addRule(Form::RANGE, 'Cena musí být v rozmezí 0 až 1000.', [0, 1000000]);

        $form->addSubmit('send', 'Přidat');

        $form->onSuccess[] = [$this, 'addProductItem'];
        return $form;
    }

    public function addProductItem(Form $form, \stdClass $values): void
    {
      $user = $this->getUser();
        
        if($user->isInRole('admin')) {

          //nalezeni polozky v objednavce
          $orderController = new OrderController($this->database);
          if ($orderController->isProductItemPresentInOrder((int) $values->orderId, $values->productId))
          {
              $this->flashMessage('Položka již existuje v objednávce.', 'alert-danger');
          } else {
              $sql = $this->database->query('INSERT INTO vm_orderDetails (OrderId, ProductId, Quantity, Price) 
                                            VALUES (?, ?, ?, ?)', $values->orderId, $values->productId, $values->quantity, $values->price);
                                            $this->setView('detail');

              if($sql->getRowCount()>0) {
                $this->flashMessage('Úspěšně vloženo.', 'alert-success');  
              } else {
                $this->flashMessage('Nelze vložit.', 'alert-danger');
              }    
          }

      } else {
        throw new Nette\Application\BadRequestException('Nemáte práva administrátora.', 403);
      }

    }

    public function actionViewpdf($orderId): void
    { 
      $orderController = new OrderController($this->database);
      $order = $orderController->getOrder($orderId);
      $orderDetails = $orderController->getOrderDetails($orderId);
      //$pdfOutputController = new PDFOutputController();
      //$pdfOutputController->viewPDF($order, $orderDetails);
      //var_dump($order);
      //var_dump($orderDetails);
      //$PDFOutputController = new PDFOutputController ($this->database);

      date_default_timezone_set('Europe/Prague');
      $date = date("d. m. Y");
      $datePlusMonth = date("d. m. Y", strtotime("+30 days"));

      $template = $this->createTemplate();
      $template->setFile(__DIR__ . "/templates/Pdf/pdf.latte");
      $template->orderId = $orderId;
      // Tip: In template to make a new page use <pagebreak>

      $pdf = new \Joseki\Application\Responses\PdfResponse($template);

      // optional
      $pdf->setSaveMode(PdfResponse::INLINE);
      $pdf->documentTitle = date("Y-m-d") . " faktura".$orderId; // creates filename 2012-06-30-my-super-title.pdf
      $pdf->pageFormat = "A4"; // wide format
      $pdf->getMPDF()->setFooter("|© www.mysite.com|"); // footer
      
      // do something with $pdf
      $this->sendResponse($pdf);


    }

}
