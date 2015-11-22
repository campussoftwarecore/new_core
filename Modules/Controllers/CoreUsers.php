<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CoreUsers
 *
 * @author ramesh
 */
class Modules_Controllers_CoreUsers extends Core_Controllers_NodeController
{
    function __construct() 
    {
        
    }
    public function validateLoginAction() 
    {
        echo "<pre>";
        print_r($this);
        echo "</pre>";
    }
    //put your code here
}
