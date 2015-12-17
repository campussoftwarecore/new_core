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
class Core_Modules_CoreDevelopmentsettings_Models_CoreFormSettings extends Core_Model_Node
{
    //put your code here
    public function coreFormSettingsOnchange()
    {        
        $events=array();
        $events['core_node_settings_id']="defaultphpfile('".$this->_nodeName."','".$this->_currentAction."','".$this->_nodeName."','parent');";           
        return $events;
    }

}