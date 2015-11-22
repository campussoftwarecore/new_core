<?php

class Core_Model_NodeProperties  
{
    public $_nodeName;
    public $_globalNodeStructure;
    public $_profileId=NULL;

    public function __construct($nodeName=NULL)
    {
        $this->_nodeName=$nodeName;        
    }
    public function nodeSettings()
    {
        $wp=new Core_WebsiteSettings();    
        $filename=$wp->documentRoot."var/".$wp->identity."/nodestructure.json";
        $fp=fopen($filename,"r");
        $filecontent=  fread($fp,filesize($wp->documentRoot."var/".$wp->identity."/nodestructure.json"));
        //$this->_globalNodeStructure=json_decode($filecontent,true);
        return json_decode($filecontent,true);
    }
    public function getNodeFileProperties()
    {
        $wp=new Core_WebsiteSettings();    
        $filename=$wp->documentRoot."var/".$wp->identity."/nodefiles.json";
        $fp=fopen($filename,"r");
        $filecontent=  fread($fp,filesize($wp->documentRoot."var/".$wp->identity."/nodefiles.json"));
        return json_decode($filecontent,true);
    }
    public function getDefaultLabels()
    {
        $wp=new Core_WebsiteSettings();    
        $filename=$wp->documentRoot."var/".$wp->identity."/language.json";
        $fp=fopen($filename,"r");
        $filecontent=  fread($fp,filesize($wp->documentRoot."var/".$wp->identity."/language.json"));
        
        return json_decode($filecontent,true);
    }
    public function getCurrentProfilePermission($profile_id="ROOT")
    {
        $wp=new Core_WebsiteSettings();    
        $filename=$wp->documentRoot."var/".$wp->identity."/profileacess.json";
        $fp=fopen($filename,"r");
        $filecontent=  fread($fp,filesize($wp->documentRoot."var/".$wp->identity."/profileacess.json"));
        
        return json_decode($filecontent,true)[$profile_id];
    }
    public function getCurrentProfilePermissionNodeAction()
    {
        $profileDetails=$this->getCurrentProfilePermission($this->_profileId);
        $actions=$profileDetails[$this->_nodeName];
        return $actions['action_name_code'];
    }
    public function getLableNames()
    {
        $wp=new Core_WebsiteSettings();    
        $filename=$wp->documentRoot."Labels/".$wp->identity."/english.phtml";
        
        if(file_exists($filename))
        {
            include_once $filename;
        }        
        return true;
    }
    public function setNode($node)
    {        
        $this->_nodeName=$node;        
    }
    public function setProfile($profile)
    {
        $this->_profileId=$profile;
    }
    public function currentNodeStructure()
    {
        $nodeStructure=$this->nodeSettings();
        return $nodeStructure[$this->_nodeName];
    }
    public function getNodeDetails()
    {
        $nodeProperties=$this->getNodeFileProperties();
        return $nodeProperties[$this->_nodeName];
    }    
    public function getActionType($action=NULL)
    {
        $wp=new Core_WebsiteSettings();    
        $filename=$wp->documentRoot."var/".$wp->identity."/actiontype.json";
        $fp=fopen($filename,"r");
        $filecontent=  fread($fp,filesize($filename));
        return json_decode($filecontent,true)[$action];
    }
}