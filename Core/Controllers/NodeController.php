<?php
class Core_Controllers_NodeController extends Core_Model_Node
{
    public $_requestedData=array();
    public $_filesData=array();
    public $_nodeName=NULL;
    public $_currentAction="Admin";
    public $_websiteAdminUrl=NULL;
    public $_methodType=NULL;    
    public $_performMraAction=NULL;
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
    public function setMraActionPerform()
    {
        $this->_performMraAction=1;
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
        if($this->_methodType=='POST')
        {
            
            $output=array();
            $output['status']="error";
            $output['errors']=$this->getLabel($this->_currentAction)." is Not Existing";
            echo json_encode($output);
            exit;
        }
        else
        {
            $this->loadLayout("noActionFound.phtml");
        }
    }
    public function checkSession()
    {
        $session=new Core_Session();
        $session=$session->getSessionMaganager();
    }
    public function addAction()
    {       
        
        $backUrl=$this->_websiteHostUrl;
        if($this->_parentNode)
        {
            $backUrl=$this->_websiteAdminUrl.$this->_parentNode."/".$this->_parentAction."/".$this->_parentSelector."/MTO/".$this->_nodeName;
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
                    $methodName=Core::covertStringToMethod($this->_nodeName."_beforeDataUpdate");
                    if(Core::methodExists($this, $methodName))
                    {
                        $data=$this->$methodName($data);
                    }
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
                $backUrl=$this->_websiteAdminUrl.$this->_parentNode."/".$this->_parentAction."/".$this->_parentSelector."/MTO/".$this->_nodeName;
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
                    $methodName=Core::covertStringToMethod($this->_nodeName."_beforeDataUpdate");
                    if(Core::methodExists($this, $methodName))
                    {
                        $data=$this->$methodName($data);
                    }
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
    public function checkDetleteData()
    {
        $np=new Core_Model_NodeProperties();
        $np->setNode($this->_nodeName);
        $childrelations=$np->getChildRelations();
        if(Core::countArray($childrelations)>0)
        {
            foreach($childrelations as $node=>$colNameArray)
            {
                $np=new Core_Model_NodeProperties();
                $np->setNode($node);
                $nodeStructure=$np->currentNodeStructure();
                $nodetablename=$nodeStructure['tablename'];
                $nodeprimkey=$nodeStructure['primkey'];
                $db=new Core_DataBase_ProcessQuery();
                $db->setTable($nodetablename,$node);
                $db->addField("count(".$node.".".$nodeprimkey.")");
                $where=array();
                foreach($colNameArray as $colName)
                {
                    $where[]=$node.".".$colName." = '".$this->_currentSelector."'";
                }
                $db->addWhere("(".Core::covertArrayToString($where, " || ").")");
                $db->buildSelect();
                $count=$db->getValue();
                if($count>0)
                {
                    return  FALSE;
                }
            }
        }        
        return TRUE;
    }
    public function deleteAction()
    {
        try
        {
            if($this->_currentSelector)
            {
                $backUrl=$this->_websiteHostUrl;
                if($this->_parentNode)
                {
                    $backUrl=$this->_websiteAdminUrl.$this->_parentNode."/".$this->_parentAction."/".$this->_parentSelector."/MTO/".$this->_nodeName;
                }
                $deleteCheck=$this->checkDetleteData();
                if($deleteCheck)
                {   
                    $nodeDelete=new Core_Model_NodeDelete();
                    $nodeDelete->setNode($this->_nodeName);
                    $nodeDelete->setPkValue($this->_currentSelector);
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
                        if($this->_performMraAction)
                        {
                            return json_encode($output);
                        }
                        else
                        {
                            echo json_encode($output);
                        }
                    }
                }
                else 
                {
                    $output=array();
                    $output['status']="error";                    
                    $output['redirecturl']=$backUrl;
                    $message=" Record Con't Deleted ";
                    $output['error']=$message;
                    if($this->_methodType=='REQUEST')
                    {
                        Core::redirectUrl($backUrl,$message);
                    }
                    else
                    {
                        if($this->_performMraAction)
                        {
                            return json_encode($output);
                        }
                        else
                        {
                            echo json_encode($output);
                        }
                    }                    
                }
            }
            
        }
        catch (Exception $ex) 
        {
            Core::Log(__METHOD__."  ".$ex->getMessage());
        }
        return;
    }
    public function descriptorAction()
    {
        $nodeRelations=$this->_nodeMTORelations;
        $requestedData=$this->_requestedData;        
        $sourceNode=$this->_requestedData['node'];
        $DestinationNode=$this->_requestedData['destinationNode'];         
        $FieldName=$this->_requestedData['idname'];  
	$noderesult=$this->_requestedData['noderesult'];
        $methodName=CoreClass::getMethod($this,"descriptorAction",$sourceNode,$FieldName); 
        $idName=$this->_requestedData['idname'];  
        $active_status=Core::inArray("active_status", $this->_boolAttributes);                
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
            $sourceNodeObj->setActionName($requestedData['action']);
            $sourceNodeObj->setActionName($requestedData['action']);
            $sourceNodeObj->setActionName($requestedData['action']);
            $sourceNodeStructure=$sourceNodeObj->_currentNodeStructure;
            $onchangeEvents=$sourceNodeObj->defaultOnchangeEvents($FieldName);
            $eventmethod=lcfirst(str_replace(" ","",ucwords(str_replace("_", " ",$sourceNode)))."Onchange");            
            if(Core::methodExists($sourceNodeObj, $eventmethod))
            {
                $customonchangeEvents=$sourceNodeObj->$eventmethod(); 
                if(count($customonchangeEvents)>0)
                {
                    foreach ($customonchangeEvents as $key => $value) 
                    {
                       if(Core::keyInArray($key, $onchangeEvents)) 
                       {
                           $onchangeEvents[$key]=$onchangeEvents[$key].$value;
                       }
                       else
                       {
                           $onchangeEvents[$key]=$value;
                       }
                    }
                }
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
            if(Core::keyInArray($this->_descriptor, $nodeRelations))
            {                
                $np=new Core_Model_NodeProperties();
                $np->setNode($nodeRelations[$this->_descriptor]);
                $parentNodeStructure=$np->currentNodeStructure();
                $db->addFieldArray(array($nodeRelations[$this->_descriptor].".".$parentNodeStructure['descriptor']=>"pds"));
                $joinCondition=$this->_nodeName.".".$this->_descriptor."=".$nodeRelations[$this->_descriptor].".".$parentNodeStructure['primkey'];
                $db->addJoin($this->_descriptor,$parentNodeStructure['tablename'],$nodeRelations[$this->_descriptor],$joinCondition);               
            }
            else 
            {
                $db->addFieldArray(array($this->_nodeName.".".$this->_descriptor=>"pds"));
            }
                        
            if(in_array($FieldName,$readonlyAttributes) || $requestedData['action']=='view' || $parentCol==1)
            {
                $defaultValue_list=Core::covertStringToArray($defaultValue,"|");
                $db->addWhere("LOWER(".$this->_nodeName.".".$this->_primaryKey.") in ('".implode("','",$defaultValue_list)."')");
            }   
            $depentparentList=$sourceNodeObj->defaultDependeeFilter($FieldName); 
            $queryExecuteFlag=1;            
            if(Core::countArray($depentparentList)>0)
            {
                foreach ($depentparentList as $parentDependentColname) 
                {
                    if(Core::keyInArray($parentDependentColname, $requestedData))
                    {
                        if($requestedData[$parentDependentColname]=="")
                        {
                            $queryExecuteFlag=0;                            
                        }
                        else
                        {
                            $methodName=CoreClass::getMethod($sourceNodeObj,"descriptionFilter",$sourceNode,$FieldName); 
                            if($methodName) 
                            { 
                                $db->addWhere($sourceNodeObj->$methodName());
                            }
                            else
                            {
                                $db->addWhere($this->_nodeName.".".$parentDependentColname."='".$requestedData[$parentDependentColname]."'");
                            }
                        }                       
                    }
                    
                }                
            }
            if($active_status && $this->_currentAction=='add')
            {
                $db->addWhere($this->_nodeName.".active_status='1'");
            }
            $methodName=CoreClass::getMethod($this,"filter",$sourceNode,$FieldName);       
            if($methodName) 
            { 
                $db->addWhere($this->$methodName());
            }
            $db->addOrderBy($this->_descriptor);
            $db->buildSelect();   
            if($queryExecuteFlag)
            {
                $result=$db->getRows();        
            }
            else
            {
                $result=array();
            }
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
                if(Core::keyInArray($FieldName, $onchangeEvents))
                {
                    $attribute->setOnchange($onchangeEvents[$FieldName]);
                }
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
        if($this->_isDefaultCollection==1)
        {
            $this->setSingleActions();  
            $this->setIndividualActions();
            $this->setMraActions();
            $this->getCollection();
            $this->setCurrentNodeName($this->_nodeName);
            $this->actionRestriction();
            $this->loadLayout("maingrid.phtml");
        }
    }    
    public function adminRefreshAction()
    {        
        $this->setSingleActions();  
        $this->setIndividualActions();
        $this->setMraActions();
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
        $NodeFieldAttributes=$this->_NodeFieldAttributes;
        $nodeResult=  json_decode($requestedData['noderesult'],true);     
        $mandotatoryAttributes =$this->mandotatoryAttributes();
        $methodName=Core::covertStringToMethod($this->_nodeName."_nodeDataValidateBefore");
        if(Core::methodExists($this, $methodName))
        {
            $errorsArray=$this->$methodName($errorsArray);
        }
        if(Core::countArray($mandotatoryAttributes)>0)
        {
            foreach ($mandotatoryAttributes as $fieldName)
            {
                if($fieldName!="")
                {
                    
                    if($requestedData[$fieldName]=="")
                    {
                        $attributeType="";
                        if(Core::keyInArray($fieldName, $NodeFieldAttributes))
                        {
                            $attributeType=$NodeFieldAttributes[$fieldName];
                        }
                        if($attributeType=='file')
                        {
                            if($nodeResult[$fieldName]=="")
                            {
                                if($this->_filesData[$fieldName]['name']=="")
                                {
                                    $errorsArray[$fieldName]=" Please Upload  ".$this->getLabel($fieldName);
                                }
                            }
                            else 
                            {
                                if($this->_requestedData['check_'.$fieldName]==1)
                                {
                                    if($this->_filesData[$fieldName]['name']=="")
                                    {
                                        $errorsArray[$fieldName]=" Please Upload  ".$this->getLabel($fieldName);
                                    }
                                }
                            }
                        }
                        else
                        {
                            $errorsArray[$fieldName]=" Please Enter ".$this->getLabel($fieldName);                
                        }
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
        $methodName=Core::covertStringToMethod($this->_nodeName."_nodeDataValidateAfter");
        if(Core::methodExists($this, $methodName))
        {
            $errorsArray=$this->$methodName($errorsArray);
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
                $db->addWhere($table.".".$this->_primaryKey."='".$requestedData['parent']."'");
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
    function mradeleteAction()
    {
        $pidname=$this->_nodeName.'_selector';
        $primaryids=Core::covertStringToArray($this->_requestedData[$pidname],'|');
        foreach ($primaryids as $pid) 
        {
            $node=CoreClass::getController($this->_nodeName,$this->_currentNodeModule,"delete");              
            $node->setActionName("delete");
            $node->setParentNode($parentNode);
            $node->setParentValue($parentValue);
            $node->setParentAction($parentAction);
            $node->setSurrentSelector($pid);
            $node->setMethodType("POST"); 
            $node->setMraActionPerform();
            $node->checkSession();
            $functionName="deleteAction";
            $node->$functionName();
            
        }
        $output=array();
        $output['status']="success";
        $output['redirecturl']=$this->_websiteHostUrl;            
        echo json_encode($output);
    }
    public function checkActionPerform()
    {
        if($this->_parentAction=='edit' || $this->_parentAction=="" )
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    public function checkMraActionPerform()
    {
        if(Core::countArray($this->_mraActions)>0)
        {        
            return true;
        }
        else
        {
            return FALSE;
        }
         
    }
    public function checkMultiEditAction()
    {
        $multiEditFields=  $this->_currentNodeStructure['editlist'];  
        if(Core::countArray(Core::covertStringToArray($multiEditFields)))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    public function checkMultiEditActionInProgress()
    {
        if(Core::keyInArray($this->_nodeName.'_multiedit', $this->_requestedData))
        {
            if($this->_requestedData[$this->_nodeName.'_multiedit']==1)
            {
                return true;
            }
            return false;
        }
        return false;
    }
    public function getMultiEditAttributes() 
    {
        return Core::covertStringToArray($this->_currentNodeStructure['editlist']);        
    }
    public function multiEditSaveAction() 
    {
            $backUrl=$this->_websiteHostUrl;
            if($this->_parentNode)
            {
                    $backUrl=$this->_websiteAdminUrl.$this->_parentNode."/".$this->_parentAction."/".$this->_parentSelector."/MTO/".$this->_nodeName;
            }
            $nodeName=$this->_nodeName;
            $multiFormData=$this->_requestedData[$nodeName.'_save'];
             
            $output=array();
            foreach ($multiFormData as $primaryValue=>$primaryData)
            {
                    $data=array();    
                    $data[$this->_primaryKey]=$primaryValue;
                    foreach($primaryData as $FieldName=>$FieldValue)
                    {                
                        $fieldNameValue=$FieldValue;
                        $data[$FieldName]=$fieldNameValue;                        
                    } 
                    $data=$this->beforeDataUpdate($data);
                     
                    $nodeSave=new Core_Model_NodeSave();
                    $nodeSave->setNode($this->_nodeName);
                    $nodeSave->setData($this->_primaryKey,$primaryValue);
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
                        }
                    }
                    $output['status']="success";
                    $output['redirecturl']=$backUrl;
            }
            $output=array();
            $output['status']="success";
            $output['redirecturl']=$backUrl;
            echo json_encode($output);
    }
	public function img_resize($ini_path, $dest_path, $params = array())
    {
		$width = !empty($params['width']) ? $params['width'] : null;
		$height = !empty($params['height']) ? $params['height'] : null;
		$constraint = !empty($params['constraint']) ? $params['constraint'] : false;
		$rgb = !empty($params['rgb']) ?  $params['rgb'] : 0xFFFFFF;
		$quality = !empty($params['quality']) ?  $params['quality'] : 100;
		$aspect_ratio = isset($params['aspect_ratio']) ?  $params['aspect_ratio'] : true;
		$crop = isset($params['crop']) ?  $params['crop'] : true;
		 
		if (!file_exists($ini_path)) return false;
		 
		 
		if (!is_dir($dir=dirname($dest_path))) mkdir($dir);
		 
		$img_info = getimagesize($ini_path);
		if ($img_info === false) return false;
		 
		$ini_p = $img_info[0]/$img_info[1];
		if ( $constraint ) {
			$con_p = $constraint['width']/$constraint['height'];
			$calc_p = $constraint['width']/$img_info[0];
		 
			if ( $ini_p < $con_p ) {
			$height = $constraint['height'];
			$width = $height*$ini_p;
			} else {
			$width = $constraint['width'];
			$height = $img_info[1]*$calc_p;
			}
		} else {
			if ( !$width && $height ) {
			$width = ($height*$img_info[0])/$img_info[1];
			} else if ( !$height && $width ) {
			$height = ($width*$img_info[1])/$img_info[0];
			} else if ( !$height && !$width ) {
			$width = $img_info[0];
			$height = $img_info[1];
			}
		}
		 
		preg_match('/\.([^\.]+)$/i',basename($dest_path), $match);
		$ext = $match[1];
		$output_format = ($ext == 'jpg') ? 'jpeg' : $ext;
		 
		$format = strtolower(substr($img_info['mime'], strpos($img_info['mime'], '/')+1));
		$icfunc = "imagecreatefrom" . $format;
		 
		$iresfunc = "image" . $output_format;
		 
		if (!function_exists($icfunc)) return false;
		 
		$dst_x = $dst_y = 0;
		$src_x = $src_y = 0;
		$res_p = $width/$height;
		if ( $crop && !$constraint ) {
			$dst_w  = $width;
			$dst_h = $height;
			if ( $ini_p > $res_p ) {
			$src_h = $img_info[1];
			$src_w = $img_info[1]*$res_p;
			$src_x = ($img_info[0] >= $src_w) ? floor(($img_info[0] - $src_w) / 2) : $src_w;
			} else {
			$src_w = $img_info[0];
			$src_h = $img_info[0]/$res_p;
			$src_y    = ($img_info[1] >= $src_h) ? floor(($img_info[1] - $src_h) / 2) : $src_h;
			}
		} else {
			if ( $ini_p > $res_p ) {
			$dst_w = $width;
			$dst_h = $aspect_ratio ? floor($dst_w/$img_info[0]*$img_info[1]) : $height;
			$dst_y = $aspect_ratio ? floor(($height-$dst_h)/2) : 0;
			} else {
			$dst_h = $height;
			$dst_w = $aspect_ratio ? floor($dst_h/$img_info[1]*$img_info[0]) : $width;
			$dst_x = $aspect_ratio ? floor(($width-$dst_w)/2) : 0;
			}
			$src_w = $img_info[0];
			$src_h = $img_info[1];
		}
		 
		$isrc = $icfunc($ini_path);
		$idest = imagecreatetruecolor($width, $height);
		if ( ($format == 'png' || $format == 'gif') && $output_format == $format ) {
			imagealphablending($idest, false);
			imagesavealpha($idest,true);
			imagefill($idest, 0, 0, IMG_COLOR_TRANSPARENT);
			imagealphablending($isrc, true);
			$quality = 0;
		} else {
			imagefill($idest, 0, 0, $rgb);
		}
		imagecopyresampled($idest, $isrc, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
		$res = $iresfunc($idest, $dest_path, $quality);
		 
		imagedestroy($isrc);
		imagedestroy($idest);
		 
		return $res;
    }
}
?>