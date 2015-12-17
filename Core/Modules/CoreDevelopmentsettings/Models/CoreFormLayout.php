<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CoreFormLayout
 *
 * @author ramesh
 */
class Core_Modules_CoreDevelopmentsettings_Models_CoreFormLayout extends Core_Model_Node
{
    //put your code here
    public function coreFormLayoutOnchange()
    {
        $events=array();
        $events['core_form_settings_id']="getFieldsForFormSettings();";           
        return $events;
    }

}
