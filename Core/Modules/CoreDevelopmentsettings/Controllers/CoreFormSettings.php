<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CoreFormSettings
 *
 * @author ramesh
 */
class Core_Modules_CoreDevelopmentsettings_Controllers_CoreFormSettings extends  Core_Controllers_NodeController
{
    public function coreFormSettingsParentFilter() 
    {          
        $requestedData=$this->_requestedData;        
        return $this->_tableName.".core_node_settings_id='".$requestedData['core_node_settings_id']."'";
    }
}