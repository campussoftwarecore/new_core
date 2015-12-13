<?php
class Core_Controllers_NodeController extends Core_Model_Node
{
    public $_requestedData=array();
    public $_filesData=array();
    public $_nodeName=NULL;
    public $_currentAction="Admin";
    public $_websiteAdminUrl=NULL;
    public $_methodType=NULL;    
    public $_removeActionRecords=array();
            
    function __construct($nodeName,$action) 
    {
        $wp=new Core_WebsiteSettings();                
        $this->setNodeName($nodeName);
        $this->setActionName($action);
        $this->_websiteHostUrl=$wp->websiteAdminUrl.$this->getNodeName()."/";
        $this->_websiteAdminUrl=$wp->websiteAdminUrl;
        $this->setShowAttributes();
        parent::__construct();
    }
    public function setMethodType($Type)
    {        
        $this->_methodType=$Type;
    }
    public function setRequestedData($requesteddata)
    {        
        $this->_requestedData=$requesteddata;
    }
    public function setFilesData($filesdata)
    {
        $this->_filesData=$filesdata;
    }
    public function adminAction()
    {
        
        $this->gridContent();        
    }
    public function noAction()
    {
        $this->loadLayout("noActionFound.phtml");
    }
    public function checkSession()
    {
        $wp=new Core_WebsiteSettings();
    }
    public function addAction()
    {       
        
        $backUrl=$this->_websiteHostUrl;
        if($this->_parentNode)
        {
            $backUrl=$this->_websiteAdminUrl.$this->_parentNode."/".$this->_parentAction."/".$this->_parentSelector;
        }
        $requestedData=$this->_requestedData;
        if($this->_methodType=="REQUEST")
        {
            $this->setCurrentNodeName($this->_nodeName);
            $loadResponse=$this->loadLayout("addform.phtml");
            if($loadResponse==false)
            {
                $loadResponse=$this->loadLayout("form.phtml");
            }
        }
        else
        {
            try
            {
                $errorsArray=$this->nodeDataValidate("add",$this);
                if(count($errorsArray)>0)
                {   
                    $output['status']="error";
                    $output['errors']=$errorsArray;
                    $output['redirecturl']=$backUrl;                 
                    echo json_encode($output);                   
                }
                else
                {                    
                    $data=array();                  
                                
                    foreach($this->_showAttributes as $FieldName)
                    {                
                        $fieldNameValue=Core::covertArrayToString($requestedData[$FieldName]);
                        $data[$FieldName]=$fieldNameValue;                        
                    } 
                    $data=$this->beforeDataUpdate($data);
                    $nodeSave=new Core_Model_NodeSave();
                    $nodeSave->setNode($this->_nodeName);
                    foreach ($data as $key=>$value)
                    {
                        $nodeSave->setData($key,$value);
                    }
                    $nodeSave->save();   
                    $method=Core::covertStringToMethod($this->_nodeName."_afterDataUpdate");
                    if(Core::methodExists($this, $method))
                    {
                        $errorsArray=$this->$method();
                        if(Core::isArray($errorsArray))
                        {
                            $output['status']="error";
                            $output['errors']=$errorsArray;
                            $output['redirecturl']=$backUrl;                 
                            echo json_encode($output);
                            exit;
                        }
                    }
                    $output=array();
                    $output['status']="success";
                    $output['redirecturl']=$backUrl;            
                    echo json_encode($output);
                }
            }
            catch (Exception $ex)
            {
                Core::Log(__METHOD__.$ex->getMessage(), $this->_nodeName."_add");
            }
        }
        
    }
    public function editAction()
    {       
        $requestedData=$this->_requestedData;
        $backUrl=$this->_websiteHostUrl;
        if($this->_parentNode)
        {
            $backUrl=$this->_websiteAdminUrl.$this->_parentNode."/".$this->_parentAction."/".$this->_parentSelector;
        }
        if($this->_methodType=="REQUEST")
        {
            
            $this->getRecordLoad();
            $this->setCurrentNodeName($this->_nodeName);
            $loadResponse=$this->loadLayout("editform.phtml");
            if($loadResponse==false)
            {
                $loadResponse=$this->loadLayout("form.phtml");
            }
        }
        else
        {            
            try
            {
                $errorsArray=$this->nodeDataValidate("edit",$this);
                if(count($errorsArray)>0)
                {   
                    $output['status']="error";
                    $output['errors']=$errorsArray;
                    $output['redirecturl']=$backUrl;                 
                    echo json_encode($output);                   
                }
                else
                {
                    $data=array();                  
                                
                    foreach($this->_showAttributes as $FieldName)
                    {                
                        $fieldNameValue=Core::covertArrayToString($requestedData[$FieldName]);
                        $data[$FieldName]=$fieldNameValue;                        
                    } 
                    $data=$this->beforeDataUpdate($data);
                    $nodeSave=new Core_Model_NodeSave();
                    $nodeSave->setNode($this->_nodeName);
                    $nodeSave->setData("id",$requestedData["id"]);
                    foreach ($data as $key=>$value)
                    {
                        $nodeSave->setData($key,$value);
                    }
                    $output=$nodeSave->save();
                    $method=Core::covertStringToMethod($this->_nodeName."_afterDataUpdate");                    
                  
                    if(Core::methodExists($this, $method))
                    {
                        $errorsArray=$this->$method();
                        if(Core::isArray($errorsArray))
                        {
                            $output['status']="error";
                            $output['errors']=$errorsArray;
                            $output['redirecturl']=$backUrl;                 
                            echo json_encode($output);
                            exit;
                        }
                    }                        
                    $output=array();
                    $output['status']="success";
                    $output['redirecturl']=$backUrl;            
                    echo json_encode($output);
                }
            }
            catch (Exception $ex)
            {
                Core::Log(__METHOD__.$ex->getMessage(), $this->_nodeName."_edit");
            }
        }
        
    }
    public function viewAction()
    {
        if($this->_methodType=="REQUEST")
        {
            
            $this->getRecordLoad();
            $this->setCurrentNodeName($this->_nodeName);
            $loadResponse=$this->loadLayout("viewform.phtml");
            if($loadResponse==false)
            {
                $loadResponse=$this->loadLayout("form.phtml");
            }
        }        
        
    }
    public function deleteAction()
    {
        $backUrl=$this->_websiteHostUrl;
        if($this->_parentNode)
        {
            $backUrl=$this->_websiteAdminUrl.$this->_parentNode."/".$this->_parentAction."/".$this->_parentSelector;
        }
       
        $nodeDelete=new Core_Model_NodeDelete();
        $nodeDelete->setNode($this->_nodeName);
        $nodeDelete->addFilterCondition("(".$this->_tableName.".".$this->_primaryKey." = '".$this->_currentSelector."'".")");           
        $nodeDelete->delete();
        $output=array();
        $output['status']="success";
        $output['redirecturl']=$backUrl;         
        if($this->_methodType=='REQUEST')
        {
            Core::redirectUrl($backUrl);
        }
        else
        {
            echo json_encode($output);
        }
        return;
    }
    public function descriptorAction()
    {
        $requestedData=$this->_requestedData;        
        $sourceNode=$this->_requestedData['node'];
        $DestinationNode=$this->_requestedData['destinationNode'];         
        $FieldName=$this->_requestedData['idname'];  
	$noderesult=$this->_requestedData['noderesult'];
        $methodName=CoreClass::getMethod($this,"descriptorAction",$sourceNode,$FieldName); 
        $idName=$this->_requestedData['idname'];  
        
        if($methodName) 
        {           
            $this->$methodName();
        }
        else            
        {
            if($noderesult!="")
            {
                $noderesult=  json_decode($noderesult,true);
            }
            else
            {
                $noderesult=array();
            }
            $defaultValue=$noderesult[$FieldName];
            $readonlyAttributes=$this->readonlyAttributes($requestedData['action']);
            $sourceNodeObj=CoreClass::getModel($sourceNode, $requestedData['action']);           
            $sourceNodeObj->setNodeName($sourceNode);            
            $sourceNodeStructure=$sourceNodeObj->_currentNodeStructure;
            $onchangeEvents=array();
            $eventmethod=lcfirst(str_replace(" ","",ucwords(str_replace("_", " ",$sourceNode)))."Onchange");
            if(Core::methodExists($sourceNodeObj, $eventmethod))
            {
                $onchangeEvents=$sourceNodeObj->$eventmethod();
            }
            $parentCol=0;
            if(Core::keyInArray("parentformNode", $requestedData))
            {
                if($requestedData['parentformvalue']!="" && $idName==$requestedData['parentformkey'])
                {
                    $defaultValue=$requestedData['parentformvalue'];
                    $parentCol=1;
                }
            }
            $multiSelectedValues=array();
            if(Core::isArray($sourceNodeStructure))
            {            
                $readonlyAttributes=Core::covertStringToArray($sourceNodeStructure['readonly_'.$requestedData['action']]);   
                $mandotatoryAttributes=Core::covertStringToArray($sourceNodeStructure['mandotatory_'.$requestedData['action']]);   
                $multiSelectedValues=Core::covertStringToArray($sourceNodeStructure['multivalues']);   
            }
            $db=new Core_DataBase_ProcessQuery();
            $db->setTable($this->_tableName, $this->_nodeName);
            $db->addField($this->_nodeName.".".$this->_primaryKey." as pid");

            if(in_array($this->_descriptor,$this->_nodeRelations))
            {

            }
            //else 
            {
                $db->addFieldArray(array($this->_nodeName.".".$this->_descriptor=>"pds"));
            }
            if(in_array($FieldName,$readonlyAttributes) || $requestedData['action']=='view' || $parentCol==1)
            {
                $defaultValue_list=Core::covertStringToArray($defaultValue,"|");
                $db->addWhere("LOWER(".$this->_nodeName.".".$this->_primaryKey.") in ('".implode("','",$defaultValue_list)."')");
            }   
            
            $methodName=CoreClass::getMethod($this,"filter",$sourceNode,$FieldName);       
            if($methodName) 
            { 
                $db->addWhere($this->$methodName());
            }
            $db->addOrderBy($this->_descriptor);
            $db->buildSelect(); 
            $result=$db->getRows();        
            try
            {       
                          
                if(in_array($idName,  $multiSelectedValues))
                {
                    $attributeType="checkbox";
                }
                else 
                {
                    $attributeType="select";
                }    
                
                $attributeDetails=new Core_Attributes_LoadAttribute($attributeType);				
                $attributeClass=Core_Attributes_.$attributeDetails->_attributeName;
                $attribute=new $attributeClass;
                $attribute->setIdName($idName);
                $attribute->setOptions($result);
                $attribute->setValue($defaultValue);

                $attribute->setAction($this->_requestedData['action']);
                if(in_array($FieldName,$mandotatoryAttributes))
                {
                    $attribute->setRequired();
                }            
                if(in_array($FieldName,$readonlyAttributes) || $requestedData['action']=='view' || $parentCol==1)
                {                
                    $attribute->setReadonly();
                }
                $attribute->loadAttributeTemplate($attributeType,$FieldName);
            }
            catch (Exception $ex)
            {
                echo $ex->getMessage();
            }
        }      
        
    }

    public function gridContent()
    {       
        
        $this->setSingleActions();  
        $this->setIndividualActions();
        $this->getCollection();
        $this->setCurrentNodeName($this->_nodeName);
        $this->actionRestriction();
        $this->loadLayout("maingrid.phtml");
    }    
    public function adminRefreshAction()
    {        
        $this->setSingleActions();  
        $this->setIndividualActions();
        $this->getCollection();
        $this->setCurrentNodeName($this->_nodeName);
        $this->actionRestriction();
        $this->loadLayout("grid.phtml");
    }

    public function setSingleActions()
    {        
        return parent::setSingleActions();
    }
    public function nodeDataValidate($action,$nodeObject)
    {
        $errorsArray=array();
        $requestedData=$nodeObject->_requestedData;       
        $nodeResult=  json_decode($requestedData['noderesult'],true);        
        foreach ($this->mandotatoryAttributes() as $fieldName)
        {
            if($requestedData[$fieldName]=="")
            {
                $errorsArray[$fieldName]=" Please Enter ".$this->getLabel($fieldName);                
            }
            else
            {
                if(Core::inArray($fieldName, $this->_numberAttributes))
                {
                    if(!is_numeric($requestedData[$fieldName]))
                    {
                        $errorsArray[$fieldName]=" Please Enter Numbers Only ";
                    }                    
                }
            }
                
        }        
        if(count($errorsArray)==0)
        {
            foreach($this->_uniqueAttributes as $fieldName)
            {
                if($requestedData[$fieldName]!="")
                {
                    $db=new Core_DataBase_ProcessQuery();            
                    $db->setTable($this->_tableName); 
                    $db->addField("*");
                    $db->addWhere($fieldName."='".$requestedData[$fieldName]."'");
                    $db->addWhere($this->_primaryKey."!='".$nodeResult[$this->_primaryKey]."'");
                    
                    $db->buildSelect();       
                    
                    $existingRecord=$db->getRow();
                    if(count($existingRecord)>0)
                    {
                        $errorsArray[$fieldName]=" Value is already Existing ";
                    }
                }
            }
        }        
        return $errorsArray;
    }
    public function beforeDataUpdate($data)
    {        
        $node=$this->_nodeName;
        $node_properties=$this->_currentNodeStructure;
        $requestedData=$this->_requestedData;
        $filesData=$this->_filesData;
        $action=$this->_currentAction;
        $table=$this->_tableName;
        $fileattribute=$node_properties['file'];
        $fileattribute_list=array();
        $filesettings_array=array();
        $file_types=array();
        $existingResult=Core::convertJsonToArray($requestedData['noderesult']);
        if(Core::keyInArray("parent_level",$this->_NodeFieldsList))
        {
            $parent_level=1;
            if($requestedData['parent']!="")
            {
                $db=new Core_DataBase_ProcessQuery();
                $db->setTable($table);
                $db->addField("parent_level");
                $db->addWhere($table.".".$this->_primaryKey.="'".$requestedData['parent']."'");
                $parent_level=$db->getValue()+1;
            }
            $data['parent_level']=$parent_level;
        }
        if($fileattribute!="")
        {
            $db=new Core_DataBase_ProcessQuery();
            $db->setTable("core_file_types");
            $db->addField("short_code");
            $db->addWhere("core_file_types.resize='1'");            
            $file_types=$db->getRows("short_code","short_code");
            
            $fileattribute_list=Core::covertStringToArray($fileattribute);            
            $db=new Core_DataBase_ProcessQuery();
            $db->setTable("core_node_filetypes");
            $db->addFieldArray(array("core_node_filetypes.colmanname"=>"colmanname"));
            $db->addFieldArray(array("core_cms_image_settings.name"=>"tempname"));
            $db->addFieldArray(array("core_cms_image_settings.witdthvalue"=>"witdthvalue"));
            $db->addFieldArray(array("core_cms_image_settings.heightvalue"=>"heightvalue"));
            $db->addJoin("core_cms_image_settings_id", "core_cms_image_settings", "core_cms_image_settings", "core_node_filetypes.core_cms_image_settings_id like concat('%',core_cms_image_settings.id,'%')");
            $db->addWhere("core_node_filetypes.core_node_settings_id='".$this->_nodeName."'");
            $db->buildSelect();
            
            $filesettings=$db->getRows();
            if(count($filesettings)>0)
            {
                foreach($filesettings as $fs)
                {
                        $filesettings_array[$fs['colmanname']][$fs['tempname']]=$fs;
                }
            }	    
        }      
        if(count($fileattribute_list)>0)
        {
            foreach($fileattribute_list as $key)
            {

                if(Core::keyInArray($key,$filesData))
                {
                    
                        $columnnamedata=$filesData[$key];
                        if($columnnamedata['name']!="")
                        {
                            $db=new Core_DataBase_ProcessQuery();
                            $db->setTable("core_node_filetypes");
                            $db->addField("core_cms_uploadfolders_id");
                            $db->addWhere("core_node_filetypes.core_node_settings_id='".$node."' and core_node_filetypes.colmanname='".$key."'");
                            $uploadfolder=$db->getValue();

                            $uploadfilepath=$columnnamedata['tmp_name'];
                            $list=explode(".",$columnnamedata['name']);
                            $extentioncount=count($list);
                            $extention=$list[$extentioncount-1];
                            $filename=strtotime(date('Y-m-d h:i:s'))."_".str_replace(array(" ","_"),array("",""),$list['0'].".".strtolower($extention));
                            $data[$key]=$filename;                            
                            $uploadfolder=Core::createFolder($uploadfolder, "U");
                            $filepath.=$uploadfolder.$filename;
                            try
                            {
                                move_uploaded_file($uploadfilepath,$filepath);		    
                            }
                            catch (Exception $ex)
                            {
                                Core::Log($ex->getMessage());
                            }
                            if(key_exists(strtolower($extention),$file_types))
                            {
                                if(key_exists($key,$filesettings_array))
                                {	    
                                    $imagesettings=$filesettings_array[$key];
                                    if(count($imagesettings)>0)
                                    {
                                            foreach($imagesettings as $tempname=>$tempdata)
                                            {                                                    
                                                $thumbfile=$uploadfolder.$tempname."_".$filename;                                                    			
                                                $params = array(
                                                'width' => $tempdata['witdthvalue'],
                                                'height' => $tempdata['heightvalue'],
                                                'aspect_ratio' => false,
                                                'crop' => false);
                                                img_resize($filepath, $thumbfile, $params);
                                            }
                                    }
                                }
                            }		    		    		    
                        }
                        else
                        {
                            if($this->_currentAction=='edit')
                            {
                                if($requestedData['check_'.$key]==1)
                                {

                                }
                                else   
                                {
                                    $fileName=$existingResult[$key];
                                    $data[$key]=$fileName;
                                }  
                            }
                        }
                }  
                else 
                {
                    if($this->_currentAction=='edit')
                    {
                        $fileName=$existingResult[$key];
                        $data[$key]=$fileName;
                    }
                }

            }  
        }
        
        return $data;
    }
    public function actionRestriction()
    {
        
        if(count($this->_individualActions)>0)
        {
            foreach ($this->_individualActions as $actionData)
            {                 
                $methodName=$actionData['code']."_".$this->_nodeName."_actionRestriction";
                $methodName=Core::covertStringToMethod($methodName);
                if(Core::methodExists($this, $methodName))
                {
                    $this->$methodName();
                }
                else
                {
                    $methodName=$actionData['code']."_actionRestriction";
                    $methodName=Core::covertStringToMethod($methodName);    
                    if(Core::methodExists($this, $methodName))
                    {
                        $this->$methodName();
                    }
                }
            }
        }        
    }
    protected  function deleteActionRestriction()
    {
        $primaryKeys=array_keys($this->_collections);
        $processkeys=$primaryKeys;        
        $restrictionKeys=array();
        if(count($processkeys)>0)
        {
            if(count($this->_nodeOTMRelations)>0)
            {
                foreach ($this->_nodeOTMRelations as $node=>$parentKey)
                {          
                    if(count($processkeys)>0)
                    {
                        $np=new Core_Model_NodeProperties();
                        $np->setNode($node);
                        $currentNodeStructure=$np->currentNodeStructure();
                        $tableName=$currentNodeStructure['tablename'];

                        $db=new Core_DataBase_ProcessQuery();
                        $db->setTable($tableName);
                        $db->addFieldArray(array("distinct(".$tableName.".".$parentKey.")"=>$parentKey));
                        $db->addWhere($tableName.".".$parentKey." in ('".Core::covertArrayToString($processkeys, "','")."')");
                        $db->buildSelect();                
                        $childRecords=$db->getRows($parentKey);  
                        $parentKeysContainsRecords=Core::getKeysFromArray($childRecords);
                        if(count($parentKeysContainsRecords)>0)
                        {                            
                            $processkeys=Core::diffArray($processkeys, $parentKeysContainsRecords);
                            $restrictionKeys=Core::mergeArrays($restrictionKeys,$parentKeysContainsRecords);
                        }
                    }
                    else
                    {
                        break;
                    }
                }
            }
        }
        $this->_removeActionRecords['delete']=$restrictionKeys;
    }
    function recordActionPerform($action,$primaryKeyValue)
    {
        $removeActionRecords=$this->_removeActionRecords[$action];
        if(Core::countArray($removeActionRecords)>0)
        {
            if(Core::inArray($primaryKeyValue, $removeActionRecords))
            {
                return false;
            }
        }              
        return true;
        
    }
    
}
?>