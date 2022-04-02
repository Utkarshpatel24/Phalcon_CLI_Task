<?php

// namespace App\Listener;


use Phalcon\Di\Injectable;
use Phalcon\Acl\Adapter\Memory;
use Phalcon\Acl\Role;
use Phalcon\Acl\Component;

use Phalcon\Security\JWT\Builder;
use Phalcon\Security\JWT\Signer\Hmac;
use Phalcon\Security\JWT\Token\Parser;
use Phalcon\Security\JWT\Validator;



class EventListener extends Injectable
{
    public function setDefaultProduct($product)
    {

        // echo "reached";
        // echo $product->getData()->name;
        // // die();
        $product = $product->getData();
        $setting = Setting :: findFirst();
        if ($setting->title == 'with tag') {
            $product->name = $product->name . $product->tags;
        }
        if ($product->price == '' || $product->price == '0') {
            $product->price = $setting->price;
        }
        if ($product->stock == '' || $product->stock == '0') {
            $product->stock = $setting->stock;
        }
        return $product;

    }

    public function setDefaultOrder($order)
    {
        $order = $order->getData();
        $setting = Setting :: findFirst();
        if ($order->zipcode == '') {
            $order->zipcode = $setting->zipcode;
        }
        return $order;
    }

    public function beforeHandleRequest($data)
    {
        //$data = $data->getData();
        
       
        $controller = $this->router->getControllerName();
        if($controller == null)
        $controller = '';
        $action = $this->router->getActionName();
        if($action == null)
        $action = '';
        $aclfile = APP_PATH. '/security/acl.cache';
        if (true != is_file($aclfile)) {
            $acl =new Memory();

          
            $acl->addRole('admin');
           
            $acl->allow('admin', '*', '*');
            

            file_put_contents(
                $aclfile,
                serialize($acl)
            );
        } else {
            $acl = unserialize(
                file_get_contents($aclfile)
            );
         
        }
        // $role =$this->request->getQuery("role");
        // $role = $role == ''? 'admin' : $role;
        // if (true === $acl->isAllowed($role, $controller, $action)) {
        //     echo "Access Granted";
        // } else {
        //     echo "Access Denied";
        //     die();
        // }
        $bearer = $this->request->getQuery('bearer');
        if ($bearer) {
            try {
                $parser =new Parser();
                $tokenObject = $parser->parse($bearer);
                $now        = new DateTimeImmutable();
                $expires  = $now->getTimestamp();
                //$expires    = $now->modify('+1 day')->getTimestamp();
                $validator = new Validator($tokenObject, 100);
                $validator->ValidateExpiration($expires);
                $claims = $tokenObject->getClaims()->getPayload();
                $role = $claims['role'];
                if (true === $acl->isAllowed($role, $controller, $action)) {
                    echo "Access Granted";
                } else {
                    echo $this->locale->_("Access Denied");
                    die();
                }
            } catch(Exception $e) {
                echo $e->getMessage();
                die();
            }
        } 
        // else {
        //     echo "Token Not Passed";
        //     die();
        // }

    }

}