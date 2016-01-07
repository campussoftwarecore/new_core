<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CoreDefaultvalues
 *
 * @author ramesh
 */
class Core_Modules_CoreDevelopmentsettings_Models_CoreDefaultvalues extends Core_Model_Node
{
    //put your code here
    public function coreDefaultvaluesOnchange()
    {
        $events=array();
        $events['core_node_settings_id']="getFieldsForDefualtFields();";           
        return $events;
    }
}
