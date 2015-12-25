<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Session
 *
 * @author ramesh
 */
class Core_Session 
{   
    private $siteObject;
    private $_sessionExists;
    public $_isProcessActive=0;
            
    function __construct() 
    {
        $this->siteObject=new Core_WebsiteSettings();
    }
    public function setProcessActive($active)
    {
        $this->_isProcessActive=$active;
    }

    private function checkSession()
    {
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        if (Core::keyInArray('HTTP_X_FORWARDED_FOR', $_SERVER))
        {
            $ipAddress = array_pop(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']));
        }
        $_SESSION[$this->siteObject->identity]['ipaddress']=$ipAddress;
        if(Core::keyInArray("profile_id",$_SESSION[$this->siteObject->identity]))
        {
            $_SESSION[$this->siteObject->identity]['_lastactivity']=  strtotime(date('Y-m-d H:i:s'));
            $this->_sessionExists=true;
        }
        else
        {
            $this->_sessionExists=false;
        }
    }

    public function getSessionMaganager()
    {       
        $this->checkSession();
        if($this->_sessionExists)
        {
            return $_SESSION[$this->siteObject->identity];
        }
        else
        {
            Core::redirectUrl("login.php");
        }
        
        
    }
    
}
