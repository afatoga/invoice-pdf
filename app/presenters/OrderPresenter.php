<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;


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
                          vm_order.StatusId, vm_user.Email, vm_orderStatus.Title 
                          FROM vm_order 
                          LEFT OUTER JOIN vm_orderStatus ON vm_order.StatusId = vm_orderStatus.Id 
                          LEFT OUTER JOIN vm_user ON vm_order.CustomerId = vm_user.Id');
                          $this->template->posts = $orderList;
          }
          else {
            //vypisuju objednavky konkretniho uzivatele
            $customerId = $user->getId();
            //var_dump($customerId);
            $orderList = $this->database->query('SELECT vm_order.Id, vm_order.InsertTime, vm_order.CustomerId, 
                         vm_order.StatusId, vm_user.Email, vm_orderStatus.Title 
                         FROM vm_order 
                         LEFT OUTER JOIN vm_orderStatus ON vm_order.StatusId = vm_orderStatus.Id 
                         LEFT OUTER JOIN vm_user ON vm_order.CustomerId = vm_user.Id
                         WHERE vm_order.CustomerId = ?', $customerId);
            $this->template->posts = $orderList->fetchAll();
            //var_dump($orderList->fetchAll());
            //$customerRoles = implode(',', $user->getRoles());
            //var_dump($customerRoles);
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

      public function renderDetail(int $id = 0): void
      { 
        echo $id;
        echo 'ahoj';
        //var_dump($_GET['orderId']);
      }

}
