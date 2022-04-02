<?php

use Phalcon\Mvc\Controller;
use Phalcon\Acl\Adapter\Memory;
use Phalcon\Security\JWT\Builder;
use Phalcon\Security\JWT\Signer\Hmac;
use Phalcon\Security\JWT\Token\Parser;
use Phalcon\Security\JWT\Validator;



class SecureController extends Controller
{
    
    public function indexAction($data = [])
    {

        // $controller = $this->router->getControllerName();
        // $action = $this->router->getActionName();
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

            if (count($data) > 0) {

                $comp = Components :: find();

                foreach($data as $key=>$val)
                {
                    //echo $key;

                    if ($key == 'role') {
                        $acl->addRole($val);
                        foreach($comp as $key1=>$val1)
                        {
                            $acl->addComponent(
                                $val1->controller,
                                [
                                    $val1->action
                                ]
                            );
                            $acl->deny($data['role'], $val1->controller, $val1->action);
                        }
                    } else {
                        foreach($comp as $key1=>$val1)
                        {
                            // echo $val1->action."<br>";
                            if ($val1->id == $val) {

                                $acl->allow($data['role'], $val1->controller, $val1->action);

                            } 
                            // else {
                            //     $acl->deny($data['role'], $val1->controller, $val1->action);
                            // }
                        }
                    }

                }
                file_put_contents(
                    $aclfile,
                    serialize($acl)
                );
               // die;
            }
         
        }
        // $role =$this->request->getQuery("role");
        // $role = $role == ''? 'admin' : $role;
        // if (true === $acl->isAllowed($role, $controller, $action)) {
        //     echo "Access Granted";
        // } else {
        //     echo "Access Denied";
        //     die();
        // }
    
    }

   

  

    public function jwtAction()
    {
        $signer  = new Hmac();

        // Builder object
        $builder = new Builder($signer);

        $now        = new DateTimeImmutable();
        $issued     = $now->getTimestamp();
        $notBefore  = $now->modify('-1 minute')->getTimestamp();
        $expires    = $now->modify('+1 day')->getTimestamp();
        $passphrase = 'QcMpZ&b&mo3TPsPk668J6QH8JA$&U&m2';

        // Setup
        $builder
            ->setAudience('https://target.phalcon.io')  // aud
            ->setContentType('application/json')        // cty - header
            ->setExpirationTime($expires)               // exp 
            ->setId('abcd123456789')                    // JTI id 
            ->setIssuedAt($issued)                      // iat 
            ->setIssuer('https://phalcon.io')           // iss 
            ->setNotBefore($notBefore)                  // nbf
            ->setSubject('my subject for this claim')   // sub
            ->setPassphrase($passphrase)                // password 
        ;

        // Phalcon\Security\JWT\Token\Token object
        $tokenObject = $builder->getToken();

        // The token
        echo $tokenObject->getToken();
    }

    
}