<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use App\Model\OrderController;


final class OrderPresenter extends Nette\Application\UI\Presenter
{
      /** @var Nette\Database\Context */
      private $database;

      public function __construct(Nette\Database\Context $database)
      {
          $this->database = $database;
      }

      public function renderIndex(string $message = ''): void
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

      /*public function renderCustomerOrderList(int $id = 0): void
      { 
        $user = $this->getUser();
        echo $user->isLoggedIn() ? 'ano' : 'ne';
        echo $user->getId();
        if (in_array('member', $user->getRoles())) {
          echo ',jsem member';
        }
        //var_dump($user);
        // get customer Id from session?
        $customerId = 2;
        $orderList = $this->database->query('SELECT vm_order.Id, vm_order.InsertTime, vm_order.CustomerId, 
                                            vm_order.StatusId, vm_user.Email, vm_orderStatus.Title 
                                            FROM vm_order 
                                            LEFT OUTER JOIN vm_orderStatus ON vm_order.StatusId = vm_orderStatus.Id 
                                            LEFT OUTER JOIN vm_user ON vm_order.CustomerId = vm_user.Id
                                            WHERE vm_order.customerId = ?', $customerId);
        $this->template->posts = $orderList;
      }*/

      public function renderDetail(int $id): void
      { 
        $user = $this->getUser();
        $orderController = new OrderController($this->database);
        
        if($orderController->isCustomersOrder($user->getId(), $id) || $user->isInRole('admin')) {

        $order = $this->database->query('SELECT vm_order.Id, vm_order.InsertTime, 
                         vm_order.StatusId, vm_orderStatus.Title AS `StatusTitle`, vm_orderDetails.ProductId, vm_orderDetails.Quantity, 
                         vm_product.Title, vm_product.Description, vm_product.Title AS `ProductTitle`, vm_product.Price
                         FROM vm_order 
                         LEFT OUTER JOIN vm_orderStatus ON vm_order.StatusId = vm_orderStatus.Id 
                         LEFT OUTER JOIN vm_orderDetails ON vm_order.Id = vm_orderDetails.OrderId
                         LEFT OUTER JOIN vm_product ON vm_orderDetails.ProductId = vm_product.Id
                         WHERE vm_order.Id = ?', $id);
            $this->template->order = $order->fetchAll();
            $this->template->orderId = $id;
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

}
