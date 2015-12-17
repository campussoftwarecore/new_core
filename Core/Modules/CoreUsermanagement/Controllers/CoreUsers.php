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
class Core_Modules_CoreUsermanagement_Controllers_CoreUsers extends Core_Controllers_NodeController
{
    //put your code here
    function checkSession()
    {
        return true;
    }
    function validateLoginAction()
    {
        $requestedData=$this->_requestedData;
        $errorsArray=array();
        $Url=$this->_websiteHostUrl.$this->_currentAction;
        if(trim($requestedData['username'])=="")
        {
            $errorsArray['username']=" Please Enter User Name";
        }
        if(trim($requestedData['userpassword'])=="")
        {
            $errorsArray['userpassword']=" Please Enter Password";
        }
        if(count($errorsArray)>0)
        {
            $output['status']="error";           
        }
        else
        {
            if(!$this->userLoginCheck())
            {
                $output['status']="error";  
                $errorsArray['username']="Enter Valid Credentials";
            }
            else
            {
                $Url=$this->_websiteAdminUrl;
                $output['status']="success";                 
            }
        }
        $output['errors']=  $errorsArray;   
        $output['redirecturl']=$Url;                 
        echo json_encode($output); 
        exit;
    }
    function userLoginCheck()
    {
        $ws=new Core_WebsiteSettings();
        if(ADMINPASS==$this->_requestedData['userpassword'] && ADMINNAME==$this->_requestedData['username'])
        {
            session_start();
            $_SESSION[$ws->identity]['profile_id']="ROOT";
            $_SESSION[$ws->identity]['name']="Ramesh";
            $_SESSION[$ws->identity]['last_activity']=time();
            return true;
        }
        else
        {
            $db=new Core_DataBase_ProcessQuery();
            $db->setTable($this->_tableName);
            $db->addField("*");
            $db->addWhere("username='".$this->_requestedData['username']."' and password='".$_REQUEST['userpassword']."'");
            $db->buildSelect(); 
            
            $userData=$db->getRow();  
            if(count($userData)>0)
            {
                $_SESSION[$ws->identity]['profile_id']=$userData['core_profile_id'];
                $_SESSION[$ws->identity]['name']=$userData['name'];
                $_SESSION[$ws->identity]['last_activity']=time();
                return true;
            }
            return false;
        
        }
    }
}
