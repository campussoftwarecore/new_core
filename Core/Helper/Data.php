<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Data
 *
 * @author ramesh
 */
class Core_Helper_Data 
{
    //put your code here
    public function getSequenceCode($Identity)
    {
        try
        {
            $db =new Core_DataBase_ProcessQuery();
            $db->setTable("core_settings_sequence");
            $db->addFieldArray(array("identity"=>"identity","prefix"=>"prefix","left_padding"=>"left_padding","current_sequence"=>"current_sequence"));
            $db->addWhere("identity='".$Identity."'");
            $db->addForUpdate();
            $settings_sequence=$db->getRow();
            $left_padding=$settings_sequence['left_padding'];
            if($left_padding=="")
            {
                $left_padding="1";
            }
            $value=$settings_sequence['prefix'].str_pad($settings_sequence['current_sequence'], $left_padding, "0", STR_PAD_LEFT);
            return $value;  
        } 
        catch (Exception $ex) 
        {
            Core::Log($ex->getMessage(),"sequenceerror.log");
        }
    }
    public function updateSequenceCode($Identity)
    {
        try 
        {
            $db =new Core_DataBase_DbConnect();
            $sql="update settings_sequence set current_sequence=current_sequence+1 where identity='".$Identity."' ";
            $db->executeQuery($sql);
        } 
        catch (Exception $ex) 
        {
            Core::Log($ex->getMessage(),"sequenceerror.log");
        }       
              
    }
}
