<?php  
    error_reporting(0);
    include_once 'Boostrap.php';
    //$refreshCache=new Core_Cache_Refresh();
    //$refreshCache->refreshCache();
   
    $extension=substr($_REQUEST['reditectpath'], -3);     
    if($extension==".js" || $extension=="css")
    {
       exit; 
    }
    global $globalnode_settings_details;
    global $nodefiledetails;    
    if($_REQUEST['logout'])
    {
        unset($_SESSION);
        session_destroy();
        $page=new Core_Pages_PageLayout();
        $page->loadLayout("login.phtml");
        exit;
    }    
    global $currentNode;  
    
    $header=true;
    $navigation=true;
    $footer=true;
    $currentProfileCode="ROOT";
    $methodType="REQUEST";
    try
    {
        $np=new Core_Model_AdminSettings($_REQUEST,$_FILES);
        $currentNode=$np->_currentNode;  
        $currentAction=$np->_currentAction;
        $currentModule=$np->_nodeDetails['module'];
        $currentModuleDisplay=$np->_nodeDetails['moduledisplay'];
        $currentRootModule=$np->_nodeDetails['rootmodule'];
        $currentSelector=$np->_currentSelector;
        if($currentAction!="")
        {
            $action=$currentAction;
        }
        else
        {
            $action="admin";
        }       
        if(count($_POST)>0)
        {
           $methodType="POST";
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
            $page=new Core_Pages_NavigationPage();
        }     
        if($currentNode!="")
        {            
            $currentClassName=  str_replace(" ","",ucwords(str_replace("_"," ",$currentNode)));

            $fileName=$rootObj->documentRoot."Modules/Controllers/".$currentClassName.".php";
            if(file_exists($fileName))
            {
                $className="Modules_Controllers_".$currentClassName;                
            }   
            else
            {
                $className="Core_Controllers_NodeController";
            }            
            $node=new $className($currentNode,$action);
            $node->setActionName($action);
            $node->setSurrentSelector($currentSelector);
            $node->setMethodType($methodType);
            $node->setRequestedData($_REQUEST);
            $node->setFilesData($_FILES);
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