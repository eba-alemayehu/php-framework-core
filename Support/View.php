<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2/16/19
 * Time: 1:03 AM
 */

namespace Application\Support;
use App\Role;
use \Application\Support\Auth;
use App\User;

class View
{
    public $file;
    public $data;
    public function __construct($file)
    {
        $file = str_replace(".", "/", $file);
        $this->file = APPLICATION_ROOT."app/Resource/views/".$file.".php";
        $this->data = [];
    }

    public static function view($file){
        return new View($file);
    }

    public function with($var, $val){
        $this->data[$var] = $val;
        return $this;
    }

    public function show(){
//        $r = new Role();
//        if(Auth::check())
//            $role = $r->find(Auth::id());
//        else
//            $role = null;

        foreach($this->data as $key=>$val){
            $$key = $val;
        }
        $session = new Session();
//        $auth = new User();
//        if(isset($_SESSION['id']))
//            $auth = $auth->find($_SESSION['id']);
//        else
//            $auth = null;
        return require($this->file);
    }
}