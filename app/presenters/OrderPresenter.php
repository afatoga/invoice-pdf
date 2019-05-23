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
        $orderList = $this->database->query('SELECT vm_order.Id, vm_order.InsertTime, vm_order.CustomerId, vm_order.StatusId, vm_user.Email, vm_orderStatus.Title 
        FROM vm_order 
        LEFT OUTER JOIN vm_orderStatus ON vm_order.StatusId = vm_orderStatus.Id 
        LEFT OUTER JOIN vm_user ON vm_order.CustomerId = vm_user.Id');
        $this->template->posts = $orderList;
      }

      public function renderDetail(int $id = 0): void
      { 
        echo $id;
        echo 'ahoj';
        //var_dump($_GET['orderId']);
      }

}
