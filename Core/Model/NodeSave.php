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
        $mts=new Core_Model_TableStructure();
        $mts->setTable($this->_tableName);
        $tableStructure=$mts->getStructure();        
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
        $datetime=date('Y-m-d H:i:s');
        $action="edit";
        if(Core::keyInArray("updatedat", $tableStructure))                
        {
            $db->addFieldArray(array("updatedat"=>$datetime));
        }
        if($buildUpdate==1)
        {
            $db->buildUpdate();
        }
        else
        {     
            $action="add";
            if(Core::keyInArray("createdat", $tableStructure))                
            {
                $db->addFieldArray(array("createdat"=>$datetime));
            }
            $db->buildInsert();            
        }
        $db->executeQuery();   
        if($this->_autoKey)
        {
            if(!$this->_tableFieldWithData[$this->_autoKey])
            {
                $newDb =new Core_DataBase_ProcessQuery();
                $newDb->setTable($this->_tableName);
                $newDb->addFieldArray(array("max(".$this->_tableName.".".$this->_autoKey.")"=>"lastinsert"));
                $newDb->buildSelect();                
                $this->_tableFieldWithData[$this->_autoKey]=$newDb->getValue();                
            }
        }
        try
        {
            $session=new Core_Session();
            $session->setProcessActive();
            $session=$session->getSessionMaganager();    
            $host_ip=$session['ipaddress'];
            $ns=new Core_DataBase_ProcessQuery();
            $ns->setTable("core_node_history");
            $ns->addFieldArray(array("node_id"=>$this->_nodeName,"table_name"=>$this->_tableName,"pk_value"=>$this->_tableFieldWithData[$this->_pkName],"core_node_actions_id"=>$action,"datetime"=>$datetime,"core_user_id"=>$core_user_id,"host_ip"=>$host_ip));
            $ns->buildInsert();
            $ns->executeQuery();
        }
        catch(Exception $ex)
        {
            Core::Log(__METHOD__.$ex->getMessage(),"NodeSave.log");
        }
        
        return $this->_tableFieldWithData[$this->_pkName];
    }
    //put your code here
}
