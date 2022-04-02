<?php

use Phalcon\Mvc\Controller;


class AddProductController extends Controller
{
    public function indexAction()
    {
        
     
    }

    public function registerAction()
    {
        $postdata = $this->request->getPost();
        print_r($postdata);
        $product = new Products();
        $product->assign(
            $this->request->getPost(),
            [
                'name',
                'description',
                'tags',
                'price',
                'stock'
            ]
        );
        $product = $this->eventManager->fire("main:setDefaultProduct", $this, $product);
        
        // $product->save();
        $success = $product->save();
        $this->view->success = $success;
        if ($success) {
              $message = "Product added Successfully!";
        } else {
              $message = "Sorry, the following problems were generated:<br>"
                       . implode('<br>', $user->getMessages());
        }
        $this->view->message = $message;

        
    }
    public function productlistAction()
    {
        $this->view->product = Products :: find();
    }
}