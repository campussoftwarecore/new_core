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
class Core_Modules_CoreDevelopmentsettings_Controllers_CoreFormLayout extends Core_Controllers_NodeController
{
    //put your code here
    public function getStructureAction()
    {
        $requestedData=$this->_requestedData;
        
        $core_form_settings_id=$requestedData['core_form_settings_id'];
        $db=new Core_DataBase_ProcessQuery();
        $db->setTable("core_form_settings");
        $db->addField("core_node_settings_id");
        $db->addWhere("id='".$core_form_settings_id."'");
        $db->buildSelect();
        $nodeName=$db->getValue();
        $defaultValue=$requestedData[$requestedData['idname']];
        $np=new Core_Model_NodeProperties();
        $np->setNode($nodeName);
        $nodestructure=$np->currentNodeStructure();
        
        $tb=new Core_Model_TableStructure();
        $tb->setTable($nodestructure['tablename']);
        $tableStructure=$tb->getStructure();
        $tableStructure=Core::getKeysFromArray($tableStructure);
        $tableStructure=Core::diffArray($tableStructure, $this->_defaulthideAttributes);
        
        $result=array();
        $i=0;
        foreach ($tableStructure as $key) 
        {
            $result[$i]['pid']=$key;
            $result[$i]['pds']=$this->getLabel($key);
            $i++;
        }
        $attributeType="select";        
        $attributeDetails=new Core_Attributes_LoadAttribute($attributeType);				
        $attributeClass=Core_Attributes_.$attributeDetails->_attributeName;
        $attribute=new $attributeClass;
        $attribute->setIdName($requestedData['idname']);
        $attribute->setOptions($result);
        $attribute->setValue($defaultValue);        
        $attribute->loadAttributeTemplate($attributeType,$requestedData['idname']);
    }
}
