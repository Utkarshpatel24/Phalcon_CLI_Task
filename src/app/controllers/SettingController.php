<?php

use Phalcon\Mvc\Controller;


class SettingController extends Controller
{
    public function indexAction()
    {
    
    }

    public function registerAction()
    {
        $postdata = $this->request->getPost();
        print_r($this->request->getPost());
        $setting = Setting :: findFirst();
        $setting->title = $postdata['title'] == 0 ? 'with tag' : 'without tag';
        $setting->price = $postdata['price'];
        $setting->stock = $postdata['stock'];
        $setting->zipcode = $postdata['zipcode'];
        $success = $setting->update();
        // $success = $product->save();
        $this->view->success = $success;

        if ($success) {
            $message = "Setting Updated Successfully!";
        } else {
            $message = "Sorry, the following problems were generated:<br>"
                     . implode('<br>', $user->getMessages());
        }

        // passing a message to the view
        $this->view->message = $message;
    }
}