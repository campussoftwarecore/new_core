<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of NodeInsert
 *
 * @author ramesh
 */
class Core_Model_NodeSave extends Core_Model_Settings
{

    public function save()
    {
        $db=new Core_DataBase_ProcessQuery();            
        $db->setTable($this->_tableName);              
        if(count($this->_tableFieldWithData)>0)
        {
            foreach ($this->_TableStructure as $key => $Data) 
            {
                $updatedKeys=array_keys($this->_tableFieldWithData);
                if(in_array($key,$updatedKeys) && $this->_autoKey!=$key)
                {
                    $db->addFieldArray(array($key=>$this->_tableFieldWithData[$key]));
                }
            }                        
            
        }
        $buildUpdate=0;
        if($this->_autoKey!="")
        {
            if($this->_autoKey==$this->_pkName)
            {
                if($this->getId())
                {
                    $buildUpdate=1;
                    $db->addWhere($this->_pkName."='".$this->getId()."'");
                }
            }
            else
            {
                if($this->_tableFieldWithData[$this->_autoKey])
                {
                    $buildUpdate=1;
                    $db->addWhere($this->_autoKey."='".$this->_tableFieldWithData[$this->_autoKey]."'");
                }
            }
            
        }
        else
        {
            if($this->_tableFieldWithData[$this->_pkName])
            {
                $buildUpdate=1;
                $db->addWhere($this->_pkName."='".$this->_tableFieldWithData[$this->_pkName]."'");
            }
        }
        if($buildUpdate==1)
        {
            $db->buildUpdate();
        }
        else
        {
            $db->buildInsert();
        }
        $db->executeQuery($query);
        return $this->_tableFieldWithData[$this->_pkName];
    }
    //put your code here
}
