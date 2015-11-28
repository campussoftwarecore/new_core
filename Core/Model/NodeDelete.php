<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of NodeDelete
 *
 * @author ramesh
 */
class Core_Model_NodeDelete extends Core_Model_Settings
{
    function delete()
    {
        try
        {
            $db=new Core_DataBase_ProcessQuery();          
            $db->setTable($this->_tableName);
            $db->addWhere($this->_whereCon);
            $db->buildDelete();
            $db->executeQuery();        
            return true;
        }
        catch (Exception $ex)
        {
            Core::Log(__METHOD__.$ex->getMessage(), $this->_tableName."_delete.log");
        }
    }
}