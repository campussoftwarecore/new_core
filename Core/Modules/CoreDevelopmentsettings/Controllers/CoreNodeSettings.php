<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CoreNodeSettings
 *
 * @author ramesh
 */
class Core_Modules_CoreDevelopmentsettings_Controllers_CoreNodeSettings extends Core_Controllers_NodeController
{
    //put your code here
    public function getPrimaryKeyAction() 
    {
        try
        {
            $tbmodel=new Core_Model_TableStructure();
            $tbmodel->setTable($this->_requestedData['tablename']);
            $tableStructure=$tbmodel->getStructure();
            if(count($tableStructure)>0)
            {
                $PrimaryKey="";
                $UniqueKey="";
                foreach ($tableStructure as $fieldData) 
                {
                    if($fieldData['Key']=='PRI')
                    {
                        $PrimaryKey=$fieldData['Field'];
                    }
                    if($fieldData['Key']=='UNI')
                    {
                        $UniqueKey=$fieldData['Field'];
                    }        
                }
                if($PrimaryKey=="")
                {
                    $PrimaryKey=$UniqueKey;
                }
                echo $PrimaryKey;
            }
            else 
            {
                echo "please check the table";
            }
        }
        catch(Exception $ex)
        {
            echo $ex->getMessage();
        }
        
    }
    public function getAutokeyAction() 
    {
        try
        {
            $tbmodel=new Core_Model_TableStructure();
            $tbmodel->setTable($this->_requestedData['tablename']);
            $tableStructure=$tbmodel->getStructure();            
            if(count($tableStructure)>0)
            {
                $PrimaryKey="";
                $UniqueKey="";
                foreach ($tableStructure as $fieldData) 
                {
                    if($fieldData['Extra']=='auto_increment')
                    {
                        echo $fieldData['Field'];
                    }                            
                }               
            }            
        }
        catch(Exception $ex)
        {
            Core::Log($ex->getMessage());
        }
        
    }
    public function getNodeStructureDetailsAction() 
    {
        $tbmodel=new Core_Model_TableStructure();
        $rquestedData=$this->_requestedData;
        $tbmodel->setTable($this->_requestedData['tablename']);
        $noderesult=$this->_requestedData['noderesult'];
        $tableStructure=$tbmodel->getStructure(); 
        $readonlyAttributes=$this->readonlyAttributes($this->_requestedData['action']);
        $fieldsArray=array();
        $idName=$this->_requestedData['idname'];        
        if($noderesult!="")
        {
            $noderesult=  json_decode($noderesult,true);
        }
        else
        {
            $noderesult=array();
        }
        $sourceNodeStructure=$this->_currentNodeStructure;
        if(Core::isArray($sourceNodeStructure))
        {            
            $readonlyAttributes=Core::covertStringToArray($sourceNodeStructure['readonly_'.$rquestedData['action']]);   
            $mandotatoryAttributes=Core::covertStringToArray($sourceNodeStructure['mandotatory_'.$rquestedData['action']]);   
            $multiSelectedValues=Core::covertStringToArray($sourceNodeStructure['multivalues']);   
        }
        $defaultValue=$noderesult[$idName];
        if(count($tableStructure)>0)
        {
            $PrimaryKey="";
            $UniqueKey="";
            $i=0;
            foreach ($tableStructure as $field=>$fieldData) 
            {              
                    if(!Core::inArray($field, array("id","createdby","createdat","updatedby","updatedat")))
                    {
                        $fieldsArray[$i]['pid']=$field;
                        $fieldsArray[$i]['pds']=$this->getLabel($field);
                        $i++;
                    }
            }
        }
        $attributeType="checkbox";
        $attributeDetails=new Core_Attributes_LoadAttribute($attributeType);				
        $attributeClass=Core_Attributes_.$attributeDetails->_attributeName;
        $attribute=new $attributeClass;
        $attribute->setIdName($idName);
        $attribute->setOptions($fieldsArray);
        $attribute->setValue($defaultValue);

        $attribute->setAction($this->_requestedData['action']);
        if(in_array($FieldName,$mandotatoryAttributes))
        {
            $attribute->setRequired();
        }            
        if(in_array($FieldName,$readonlyAttributes) || $rquestedData['action']=='view')
        {                
            $attribute->setReadonly();
        }
        $attribute->loadAttributeTemplate($attributeType,$FieldName);
    }
    public function coreNodeSettingsAfterDataUpdate()
    {
        $cache=new Core_Cache_Refresh();
        $cache->nodeStructure();
        $cache->profilePrivileges();
        return TRUE;  
    }
    public function coreReportsdetailsSettingsNodeIdDescriptorAction()
    {        
        $defaultValue=$this->_requestedData['node_id'];
        $result=array();
        $finalResult=array();
        if($this->_requestedData['core_reportsdetails_id'])
        {
            $db=new Core_DataBase_ProcessQuery();
            $db->setTable("core_reportsdetails");
            $db->addWhere("core_reportsdetails.id='".$this->_requestedData['core_reportsdetails_id']."'");
            $report=$db->getRow();
            $db=new Core_DataBase_ProcessQuery();
            $db->setTable("core_registernode");
            $db->addField("displayvalue");
            $db->addWhere("core_registernode.nodename='".$report['node_id']."'");
            
            $nodeDisplay=$db->getValue();
            $finalResult[]=array("pid"=>$report['node_id'],"pds"=>$nodeDisplay);
            $db=new Core_DataBase_ProcessQuery();
            $db->setTable("core_node_relations","nrl");
            $db->addFieldArray(array("rnd.nodename"=>"pid","rnd.displayvalue"=>"pds"));
            $db->addJoin("node_id", "core_registernode", "rnd", "nrl.core_node_parent=rnd.nodename");
            $db->addWhere("((nrl.core_node_settings_id='".$report['node_id']."' and nrl.core_node_parent!='".$report['node_id']."' and nrl.core_relation_type_id='MTO'))");
            $db->addGroupBy("rnd.nodename");
            $db->addOrderBy("rnd.sort_value");
            $db->buildSelect();
            $result=$db->getRows();
            foreach ($result as $rs)
            {
                $finalResult[]=$rs;
            }
        }
        $attributeType="select";        
        $attributeDetails=new Core_Attributes_LoadAttribute($attributeType);				
        $attributeClass=Core_Attributes_.$attributeDetails->_attributeName;
        $attribute=new $attributeClass;
        $attribute->setIdName('node_id');
        $attribute->setOptions($finalResult);
        $attribute->setValue($defaultValue);
        $attribute->setOnchange("getFieldsforReport();");
        $attribute->loadAttributeTemplate($attributeType,'node_id');
    }

}
