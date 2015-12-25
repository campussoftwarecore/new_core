<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CoreClass
 *
 * @author ramesh
 */
class CoreClass 
{
    static public $node=NULL;
    
    static function getController($node,$module=NULL,$action=NULL)
    {
        $className="Modules_".str_replace(" ","",ucwords(str_replace("_", " ",$module)))."_Controllers"."_".str_replace(" ","",ucwords(str_replace("_", " ",$node)));
        if(Core::fileExists(str_replace("_","/",$className).".php"))
        {
            return new $className($node,$action);
        }
        $className="Core_Modules_".str_replace(" ","",ucwords(str_replace("_", " ",$module)))."_Controllers"."_".str_replace(" ","",ucwords(str_replace("_", " ",$node)));
        if(Core::fileExists(str_replace("_","/",$className).".php"))
        {
            return new $className($node,$action);
        }
        else
        {
            Core::Log($className,"controller.log");
            return new Core_Controllers_NodeController($node,$action);
        }        
        
    }
    static function getModel($node,$action=NULL)
    {
        if($node=="")
        {
            return false;
        }
        $np=new Core_Model_Abstract();
        $np->setNodeName($node);
        $module=$np->_currentNodeModule;
        $className="Modules_".str_replace(" ","",ucwords(str_replace("_", " ",$module)))."_Models"."_".str_replace(" ","",ucwords(str_replace("_", " ",$node)));       
        if(Core::fileExists(str_replace("_","/",$className).".php"))
        {
            return new $className($node,$action);
        }
        $className="Core_Modules_".str_replace(" ","",ucwords(str_replace("_", " ",$module)))."_Models"."_".str_replace(" ","",ucwords(str_replace("_", " ",$node)));       
        if(Core::fileExists(str_replace("_","/",$className).".php"))
        {
            return new $className($node,$action);
        }
        else
        {
            return new Core_Model_Node($node,$action);
        }        
        
    }
    static function getMethod($object,$action,$node=NULL,$FieldName=NULL)
    {        
        if($node=="")
        {
            return false;
        }
        $methodName=lcfirst(str_replace(" ","",ucwords(str_replace("_", " ",$node))).str_replace(" ","",ucwords(str_replace("_", " ",$FieldName))).ucwords($action));
        
        if(Core::methodExists($object, $methodName))
        {
            return   $methodName;
        }
        $methodName=lcfirst(str_replace(" ","",ucwords(str_replace("_", " ",$node))).ucwords($action));
       
        if(Core::methodExists($object, $methodName))
        {
            return  $methodName;
        }
        return false;
    }
    //put your code here
}
