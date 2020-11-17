<?php


namespace App\Classes;
use mysqli;

class Config
{
    public $conn;

    public $base_url = 'http://dcw.test/';

    public function __construct()
    {
//        session_start();
        $this->conn = new mysqli('localhost' , 'root' , '' , 'dcw');
        if ($this->conn->connect_error){
            die($this->conn->connect_error);
        }
    }

    public function showMessage($type , $message){
        $output = '';
        $output.= '<div class="alert alert-'. $type .' alert-dismissible fade show m-0" role="alert">';
        $output.= $message;
        $output.= '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
        $output.= '<span aria-hidden="true">&times;</span>';
        $output.= '</button>';
        $output.= '</div>';
        return $output;
    }

    public function isLogedIn(){
//        session_start();
        return isset($_SESSION['user_email']);
    }

}