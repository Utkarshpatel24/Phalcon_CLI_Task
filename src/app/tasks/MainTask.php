<?php

declare(strict_types=1);

namespace App\Tasks;

use Phalcon\Cli\Task;
use Firebase\JWT\JWT;
use Orders;
use Setting;
use Products;

class MainTask extends Task
{
    public function mainAction()
    {
        echo 'This is the default task and the default action' . PHP_EOL;
    }

    public function clearLogAction()
    {
        $logfile = APP_PATH. '/logs/product.log';
        if (true == is_file($logfile)) {
            // clear($logfile);
            file_put_contents($logfile, '');
            echo "Successfully cleared" . PHP_EOL;
        } else {
            echo "File Not Present" . PHP_EOL;
        }

    }

    public function getTokenAction()
    {
        $key = "example_key";
        $payload = array(
            "iss" => "http://example.org",
            "aud" => "http://example.com",
            "iat" => 1356999524,
            "nbf" => 1357000000,
            "name" => 'utkarsh',
            "role" => 'admin'
        );
        $token = JWT::encode($payload, $key, 'HS256');
        // echo $token;
        // die();
        echo $token . PHP_EOL;
    }

    public function setDefaultSettingAction()
    {
        $setting = Setting :: findFirst();
        $setting->title = "with tag";
        $setting->price = 900;
        $setting->stock = 50;
        $setting->zipcode = 43552;
        $setting->update();
    }

    public function getCountAction()
    {
        $product = Products :: find(
            [
                'conditions' => "stock < 10",
                // "bind" => ['10']
            ]
        );
        // $product =  Products.where('stock < 10');
        echo count($product) . PHP_EOL;
    }

    public function removeAclAction()
    {
        $aclfile = APP_PATH. '/security/acl.cache';
        if (true == is_file($aclfile)) {
            unlink($aclfile);
            echo "Successfully Deleted" . PHP_EOL;
        } else {
            echo "File Not Present" . PHP_EOL;
        }
    }

    public function getRecentOrderAction()
    {
        $order = Orders :: find(
            [
                "order" => "time desc",
                "limit" => '1'
            ]
        );
        echo "Order Placed By ". $order[0]->name . PHP_EOL;
        echo "Time " . $order[0]->time . PHP_EOL;

        // print_r($order);
        // echo "done" . PHP_EOL;
    }
}