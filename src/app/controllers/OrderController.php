<?php

use Phalcon\Mvc\Controller;


class OrderController extends Controller
{
    public function indexAction()
    {
        //$this->eventManager->fire("application:beforeHandleRequest", $this);
       
    }

    public function registerAction()
    {
        $order = new Orders();
        $order->assign(
            $this->request->getPost(),
            [
                'name',
                'address',
                'zipcode',
                'product',
                'quantity'
            ]
        );
        $order = $this->eventManager->fire("main:setDefaultOrder", $this, $order);
        $success = $order->save();
        $this->view->success = $success;
        if ($success) {
              $message = "Order Placed Successfully!";
        } else {
              $message = "Sorry, the following problems were generated:<br>"
                       . implode('<br>', $user->getMessages());
        }
  
          // passing a message to the view
          $this->view->message = $message;

    }

    public function orderlistAction()
    {
        $this->view->order = Orders :: find();
    }
}