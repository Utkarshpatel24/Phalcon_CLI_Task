<?php

use Phalcon\Mvc\Controller;
use Phalcon\Security\JWT\Builder;
use Phalcon\Security\JWT\Signer\Hmac;
use Phalcon\Security\JWT\Token\Parser;
use Phalcon\Security\JWT\Validator;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;



class SignupController extends Controller
{
    public function indexAction()
    {

        $role = Roles::find();
        $disp = "";
        foreach($role as $key=>$val)
        {
            $disp.='<option value="'.$val->role.'">'.$val->role.'</option>';
        }
        $this->view->options = $disp;
        
    }

    public function registerAction()
    {
        $postdata = $this->request->getPost();
        // print_r($postdata);
        // $token = $this->getToken($postdata['role']);
        $token = $this->getTokenByFirebaseAction($postdata['name'], $postdata['role']);
        $user = new Users();
        $user->assign(
            $this->request->getPost(),
            [
                'name',
                'role',
                'email',
                'password'
            ]
        );
        $user->token = $token;
        $success = $user->save();
        if ($success) {
            $message = "SignUp Successfully!";
        } else {
            $message = "Sorry, the following problems were generated:<br>"
                     . implode('<br>', $user->getMessages());
        }

        // passing a message to the view
        $this->view->message = $message;
    }

    public function getToken($role)
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
            ->setSubject($role)   // sub
            ->setPassphrase($passphrase)                // password 
        ;

        // Phalcon\Security\JWT\Token\Token object
        $tokenObject = $builder->getToken();

        // The token
        // echo $tokenObject->getToken();
        return $tokenObject->getToken();
    }

    public function getTokenByFirebaseAction($name, $role)
    {
        $key = "example_key";
        $payload = array(
            "iss" => "http://example.org",
            "aud" => "http://example.com",
            "iat" => 1356999524,
            "nbf" => 1357000000,
            "name" => $name,
            "role" => $role
        );
        $token = JWT::encode($payload, $key, 'HS256');
        // echo $token;
        // die();
        return $token;
        
    }
}