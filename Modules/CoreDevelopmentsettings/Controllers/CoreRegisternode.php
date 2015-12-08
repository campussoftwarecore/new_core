<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CoreRegisternode
 *
 * @author ramesh
 */
class Modules_CoreDevelopmentsettings_Controllers_CoreRegisternode extends Core_Controllers_NodeController
{
    
    //put your code here
    public function coreRegisternodeCoreRootModuleIdFilter()
    {
        return $this->_nodeName.".is_module='1' and (".$this->_nodeName.".core_root_module_id='' || ".$this->_nodeName.".core_root_module_id is NULL ) ";        
    }
    public function coreRegisternodeCoreModuleDisplayIdFilter()
    {
        return $this->_nodeName.".is_module='1'";        
    }
    public function coreRegisternodeCoreModuleIdFilter()
    {
        return $this->_nodeName.".is_module='1'";        
    }
    public function coreNodeSettingsCoreRegisternodeIdFilter($param) 
    {
        return $this->_nodeName.".is_module!='1'";   
    }
    public function coreRegisternodeAfterDataUpdate()
    {
        $cache=new Core_Cache_Refresh();
        $cache->nodeFiles();
        return TRUE;        
    }
    
}
