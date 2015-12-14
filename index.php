<?php  
    error_reporting(0);
    include_once 'Boostrap.php';  
    
    $wp=new Core_WebsiteSettings();
    $extension=substr($_REQUEST['reditectpath'], -3);     
    if(Core::inArray($extension, array(".js","css","png","jpg")))
    {
       exit; 
    }
    session_start();
    global $globalnode_settings_details;
    global $nodefiledetails;    
    if($_REQUEST['logout'])
    {
        session_destroy();
        Core::redirectUrl("login.php");
    }    
    global $currentNode;
    try
    {
        $np=new Core_Model_AdminSettings($_REQUEST,$_FILES);    
        $parentNode=$np->_parentNode;
        $parentValue=$np->_parentValue;
        $parentAction=$np->_parentAction;
        $currentNode=$np->_currentNode;  
        $currentAction=$np->_currentAction;
        $currentModule=$np->_nodeDetails['module'];
        $currentModuleDisplay=$np->_nodeDetails['moduledisplay'];
        $currentRootModule=$np->_nodeDetails['rootmodule'];
        $currentSelector=$np->_currentSelector;
        $methodType=$np->_methodType;
        $currentProfileCode=$_SESSION[$wp->identity]['profile_id'];
        $header=true;
        $navigation=true;
        $footer=true;
        
        if($currentAction!="")
        {
            $action=$currentAction;
        }
        else
        {
            $action="admin";
        }       
        if($methodType=="POST")
        {            
           $header=false;
           $navigation=false;
           $footer=false;
        }
        
        if($header)
        {
            
            $page=new Core_Pages_HeaderPage();
        }
        if($navigation)
        {
            $page=new Core_Pages_NavigationPage($np);           
        }     
        if($currentNode!="")
        {  
            $node=CoreClass::getController($currentNode,$currentModule,$action);                    
            $node->setActionName($action);
            $node->setParentNode($parentNode);
            $node->setParentValue($parentValue);
            $node->setParentAction($parentAction);
            $node->setSurrentSelector($currentSelector);
            $node->setMethodType($methodType);
            $node->setRequestedData($_REQUEST);
            $node->setFilesData($_FILES);
            $node->checkSession();
            $functionName=$action."Action";           
            if(method_exists($node,$functionName))
            {
                $node->$functionName();
            }
            else
            {
                $node->noAction();
            }
            
        }
        else
        { 
            $session=new Core_Session();
            $session=$session->getSessionMaganager();            
            include_once 'pages/home.phtml';

        }
        if($footer)
        {
            $page=new Core_Pages_FooterPage();
        } 
    }
 catch (Exception $ex)
 {
     echo $e->getMessage();
 }
?>