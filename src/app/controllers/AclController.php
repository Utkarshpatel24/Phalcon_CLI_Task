<?php

use Phalcon\Mvc\Controller;


class ACLController extends Controller
{
    public function indexAction()
    {
        
    }

    public function rolesAction()
    {
        
        $this->view->message = "";
        $postdata = $this->request->getPost();
        
        if (count($postdata) != 0) {
            $role = new Roles();
            $role->assign(
                $this->request->getPost(),
                [
                    'role'
                ]
            );
            $success = $role->save();
            if($success)
            $this->view->message = "Added successfully !!!";
            
        }
    }

    public function componentAction()
    {
        $this->view->message = "";
        $postdata = $this->request->getPost();
        print_r($postdata);
        if (count($postdata) != 0) {
            $component = new Components();
            $component->assign(
                $this->request->getPost(),
                [
                    'controller',
                    'action'
                ]
            );
            $success = $component->save();
            if($success)
            $this->view->message = "Successfully Registered !!";
        }
    }

    public function permissionAction()
    {
        $postdata = $this->request->getPost(); 
       
        $role = Roles::find();
        $disp = "";
        foreach($role as $key=>$val)
        {
            $disp.='<option value="'.$val->role.'">'.$val->role.'</option>';
        }
        $this->view->options = $disp;
        $component = Components :: find();
        $disp2 = "";
        foreach($component as $key=>$val)
        {
            $disp2.='<input type="checkbox"  name="id-'.$val->id.'" value="'.$val->id.'">
            <label for="component"> '.$val->controller.' => '.$val->action.'</label><br>';
        }
        $this->view->checkbox = $disp2;

        if (count($postdata) != 0 ) {

            
            $this->dispatcher->forward(
                [
                    'controller' => 'secure',
                    'action' => 'index',
                    'params' => array($postdata)
                ]
            );

        }

    }
}