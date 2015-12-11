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
            
    function __construct() 
    {
        $this->siteObject=new Core_WebsiteSettings();
    }
    private function checkSession()
    {
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
        if($this->_sessionExists)
        {
            
        }
        
        
    }
    
}
