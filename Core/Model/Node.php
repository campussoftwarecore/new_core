<?php
    class Core_Model_Node extends Core_Model_Abstract
    {
        public $_record=array();
        public $_defaulthideAttributes=array("id","createdby","createdat","updatedby","updatedat");
        public $_whereCon=null;
        public $_totalRecordsCount=null;
        public $_rpp=null;
        public $_page=null;
        public $_wsrpp=null;
        public $_eventMethods=array();
        public $_conditionMTO=array();
        public $_isReport=0;
        public $_childNode=NULL;
        public function __construct() 
        {
            
        }   
        public function setChildNode($childNode)
        {
            $this->_childNode=$childNode;
        }
        public function setReport()
        {
            $this->_isReport=1;
        }
        public function removeReport()
        {
            $this->_isReport=0;
        }
        public function setRpp($rpp)
        {
            $this->_rpp=$rpp;
        }
        public function setPage($page)
        {
            $this->_page=$page;
        }
        public function hideAttributes()
        {            
            $this->_currentAction;
            $defaulthideAttributes=$this->_defaulthideAttributes;
            $action=$this->_currentAction;          
            if(strtolower($action)=='adminrefresh')
            {
                $action="admin";
            }
            $hide_column="hide_".strtolower($action);
            
            if(key_exists($hide_column,$this->_currentNodeStructure))
            {
                $nodehideattributes=explode("|",$this->_currentNodeStructure[$hide_column]);            
            }
            else 
            {
                $nodehideattributes=explode("|",$this->_currentNodeStructure['hide_edit']); 
            }
            
            return array_merge($defaulthideAttributes,$nodehideattributes);
        }
        public function mandotatoryAttributes()
        {            
            $this->_currentAction;            
            $mandotatory_column="mandotatory_".strtolower($this->_currentAction);
            $mandotatoryAttributes=array();
            if(key_exists($mandotatory_column,$this->_currentNodeStructure))
            {
                $mandotatoryAttributes=explode("|",$this->_currentNodeStructure[$mandotatory_column]);            
            }            
            
            return $mandotatoryAttributes;
        }
        public function readonlyAttributes($actionName=NULL)
        {            
            if($actionName=="")
            {
                $actionName=$this->_currentAction;
            }                        
            $readonly_column="readonly_".strtolower($actionName);
            $readonlyAttributes=array();
            if(key_exists($readonly_column,$this->_currentNodeStructure))
            {
                $readonlyAttributes=explode("|",$this->_currentNodeStructure[$readonly_column]);            
            }            
            else
            {
                $readonlyAttributes=array_keys($this->_NodeFieldsList);
            }            
            return $readonlyAttributes;
        }
        public function setShowAttributes()
        {
            $this->getFieldsForNode();
            $this->_showAttributes= array_diff(array_keys($this->_NodeFieldsList),$this->hideAttributes());                      
        }    
        public function getRecordLoad()
        {
            $db=new Core_DataBase_ProcessQuery();            
            $db->setTable($this->_tableName);              
            $db->addField("*");
            $db->addWhere($this->_primaryKey."='".$this->_currentSelector."'");
            $this->_record=$db->getRow();      
            
        }
        public function getCollection()
        {
            try
            {
                $this->getTotalResultCount();
                $db=new Core_DataBase_ProcessQuery();            
                $db->setTable($this->_tableName);  
                $indexKey=$this->_primaryKey;
                if(count($this->_showAttributes)>0)
                {
                    $db->addField("id");
                    foreach ($this->_showAttributes as $fieldName) 
                    {
                        if(key_exists($fieldName, $this->_nodeMTORelations))
                        {
                            if($indexKey==$fieldName)
                            {
                                $indexKey=$fieldName."pk";
                            }
                            $relationNode=  $this->_nodeMTORelations[$fieldName];                        
                            $np=new Core_Model_NodeProperties($relationNode);
                            $np->setNode($relationNode);
                            $relationNodeStructure=$np->currentNodeStructure(); 
                            
                            $nr=new Core_Model_NodeRelations();
                            $nr->setNode($relationNode);                            
                            $nodeRelations=$nr->getCurrentNodeRelation();                                                       
                            $relationNodeTable=$relationNodeStructure['tablename'];
                            $relationNodePK=$relationNodeStructure['primkey'];
                            $relationNodeDR=$relationNodeStructure['descriptor']; 
                            $db->addFieldArray(array($fieldName=>$fieldName."pk"));
                            if(in_array($fieldName, $this->_multivaluesAttributes))
                            {
                                $joinCondition=$this->_nodeName.".".$fieldName." like concat('%','|',".$fieldName.".".$relationNodePK.",'|','%') 
                    || ".$this->_nodeName.".".$fieldName." like concat(".$fieldName.".".$relationNodePK.",'|','%') 
                    || ".$this->_nodeName.".".$fieldName." like concat('%','|',".$fieldName.".".$relationNodePK.") 
                    || (".$this->_nodeName.".".$fieldName."=".$fieldName.".".$relationNodePK.")";
                                $db->addJoin($fieldName, $relationNodeTable, $fieldName,$joinCondition);
                                $db->addFieldArray(array("group_concat(distinct(".$fieldName.".".$relationNodeDR.") separator '|' )"=>$fieldName));
                            }
                            else
                            {
                                $joinCondition=$this->_nodeName.".".$fieldName."=".$fieldName.".".$relationNodePK;
                                $db->addJoin($fieldName, $relationNodeTable, $fieldName,$joinCondition);
                                if(Core::keyInArray($relationNodeDR, $nodeRelations))
                                {                
                                    $np=new Core_Model_NodeProperties();
                                    $np->setNode($nodeRelations[$relationNodeDR]);
                                    $parentNodeStructure=$np->currentNodeStructure();
                                    $db->addFieldArray(array($fieldName.$relationNodeDR.".".$parentNodeStructure['descriptor']=>$fieldName));
                                    $joinCondition=$fieldName.".".$relationNodeDR."=".$fieldName.$relationNodeDR.".".$parentNodeStructure['primkey'];
                                    $db->addJoin($fieldName.$relationNodeDR,$parentNodeStructure['tablename'],$fieldName.$relationNodeDR,$joinCondition);               
                                    $db->addFieldArray(array($fieldName.".".$relationNodeDR=>$relationNodeDR."pk"));
                                }
                                else 
                                {
                                   
                                    $db->addFieldArray(array($fieldName.".".$relationNodeDR=>$fieldName));
                                }
                                
                            }
                        }
                        else
                        {
                            $db->addField($fieldName);
                        }
                    }                
                }            
                $ws=new Core_WebsiteSettings();
                if($this->_isReport==0)
                {
                    $rpp=$ws->rpp;
                    $page=1;
                    if($this->_requestedData['rpp_'.$this->_nodeName])
                    {
                        $rpp=$this->_requestedData['rpp_'.$this->_nodeName];
                    }
                    $this->_rpp=$rpp;
                    if($this->_requestedData['page_'.$this->_nodeName])
                    {
                        $page=$this->_requestedData['page_'.$this->_nodeName];
                    }
                    $this->_page=$page;
                }
                else
                {
                    $page=$this->_page;
                }
                $this->_wsrpp=$ws->rpp;                
                $this->addFilter();
                $db->addWhere($this->_whereCon);
                $db->addGroupBy("id");
                $db->addOrderBy($this->_autoKey." DESC");
                $db->setLimit(($page-1)*$this->_rpp,$this->_rpp);     
                $db->buildSelect();    
                $this->_collections=$db->getRows($indexKey); 
            }
            catch(Exception $ex)
            {
                Core::Log($ex->getMessage(),"collections.log");
            }
        }
        public function getTotalResultCount()
        {
            $db=new Core_DataBase_ProcessQuery();            
            $db->setTable($this->_tableName); 
            $this->addFilter();
            $db->addFieldArray(array("count(distinct(".$this->_tableName.".id))"=>"count"));
            if(Core::countArray($this->_conditionMTO)>0)
            {
                foreach ($this->_conditionMTO as $FieldName => $parentNode) 
                {
                    $parentNodeDetails=new Core_Model_NodeProperties();
                    $parentNodeDetails->setNode($parentNode);
                    $parentStructure=$parentNodeDetails->currentNodeStructure(); 
                    $joinCondition=$this->_nodeName.".".$FieldName."=".$FieldName.".".$parentStructure['primkey'];
                    $db->addJoin($FieldName, $parentStructure['tablename'], $FieldName, $joinCondition);
                    $nr=new Core_Model_NodeRelations();
                    $nr->setNode($parentNode);                            
                    $nodeRelations=$nr->getCurrentNodeRelation();
                    $relationNodeDR=$parentStructure['descriptor'];
                    if(Core::keyInArray($relationNodeDR, $nodeRelations))
                    {                
                        $np=new Core_Model_NodeProperties();
                        $np->setNode($nodeRelations[$relationNodeDR]);
                        $parentNodeStructure=$np->currentNodeStructure();
                        $joinCondition=$FieldName.$relationNodeDR.".".$parentNodeStructure['primkey']."=".$FieldName.".".$relationNodeDR;
                        $db->addJoin($FieldName.$relationNodeDR.$relationNodeDR,$parentNodeStructure['tablename'],$FieldName.$relationNodeDR,$joinCondition);
                    }                    
                }
            }
            $db->addWhere($this->_whereCon);            
            $db->buildSelect();     
            $this->_totalRecordsCount=$db->getValue();             
        }
        public function nodeFieldDisplay($row,$FieldName)
        {
            $displayValue=$row[$FieldName];
            $functionName=$this->_nodeName."_".$FieldName."_nodeFieldDisplay";
            if(method_exists($this,$functionName))
            {
                $displayValue=$this->$functionName($row,$FieldName);
            }
            if(in_array($FieldName, $this->_boolAttributes))
            {
                if($displayValue==1)
                {
                    $displayValue="Yes";
                }
                else
                {
                    $displayValue="No";
                }
            }
                        
            return $displayValue;
        }
        public function loadAttribute($FieldName,$record=  array())
        {               
            $currentNodeStructure=  $this->_currentNodeStructure;
            $mandotatoryAttributes=$this->mandotatoryAttributes();			
            $readonlyAttributes=$this->readonlyAttributes();            
            $onchangeEvents=array();
            $sourceNodeObj=CoreClass::getModel($this->_nodeName, $this->_record['action']);
            $eventmethod=lcfirst(str_replace(" ","",ucwords(str_replace("_", " ",$this->_nodeName)))."Onchange");
            
            if(Core::methodExists($sourceNodeObj, $eventmethod))
            {
                
                $onchangeEvents=$sourceNodeObj->$eventmethod();
            }     
            $methodName=$FieldName."_loadAttribute";
            if(Core::methodExists($sourceNodeObj,$methodName))
            {
                $sourceNodeObj->$methodName();
            }
            else
            {
                if(Core::keyInArray($FieldName, $this->_NodeFieldAttributes))
                {
                    $attributeType=ucwords($this->_NodeFieldAttributes[$FieldName]);
                }
                else
                {    

                    if(Core::inArray($this->_NodeFieldsList[$FieldName], array("text","longtext","mediumint")))
                    {
                        $attributeType="Textarea";
                    }
                    else
                    {
                        $attributeType="Text";
                    }
                }
                try
                {   
                    $FieldNameOptions=array();
                    $actionName=$this->_currentAction;
                    if($actionName=="adminRefresh")
                    {
                        $actionName="admin";

                    }                
                    if($actionName!="admin")
                    {
                        $record=$this->_record;
                    }
                    $defaultValue=$record[$FieldName];
                    $attributeDetails=new Core_Attributes_LoadAttribute($attributeType);
                    
                    $attributeClass="Core_Attributes_".$attributeDetails->_attributeName;                
                    $attribute=new $attributeClass;
                    $attribute->setNodeName($this->_nodeName);
                    $attribute->setPkName($currentNodeStructure['primkey']);
                    $attribute->setIdName($FieldName);
                    $attribute->setValue($defaultValue);                
                    $attribute->setRecord($record);
                    if(Core::keyInArray($FieldName, $onchangeEvents))
                    {

                        $attribute->setOnchange($onchangeEvents[$FieldName]);
                    }
                    if(in_array($FieldName,$mandotatoryAttributes))
                    {
                        $attribute->setRequired();
                    }
                    if(in_array($FieldName,$readonlyAttributes))
                    {					
                        $attribute->setReadonly();                  
                    }		
                    if($actionName=="admin")
                    {
                        $multiEditFields=$this->getMultiEditAttributes(); 

                        if($this->checkMultiEditActionInProgress())
                        {

                            if(Core::inArray($FieldName, $multiEditFields))
                            {              
                                if(Core::keyInArray($FieldName, $this->_nodeMTORelations))
                                {
                                    $sourceNodeObj=CoreClass::getModel($this->_nodeMTORelations[$FieldName]);   

                                    $sourceNodeObj->setNodeName($this->_nodeMTORelations[$FieldName]);  

                                    $db=new Core_DataBase_ProcessQuery();
                                    $db->setTable($sourceNodeObj->_currentNodeStructure['tablename'], $sourceNodeObj->_nodeName);
                                    $db->addField($sourceNodeObj->_nodeName.".".$sourceNodeObj->_primaryKey." as pid");
                                    $db->addFieldArray(array($sourceNodeObj->_nodeName.".".$sourceNodeObj->_descriptor=>"pds"));                    
                                    $methodName=CoreClass::getMethod($this,"filter",$sourceNodeObj->_nodeName,$FieldName);       
                                    if($methodName) 
                                    { 
                                        $db->addWhere($this->$methodName());
                                    }
                                    $db->addOrderBy($sourceNodeObj->_descriptor);
                                    $db->buildSelect();      
                                    $FieldNameOptions=$db->getRows();
                                }
                                $attribute->setMultiEdit();                    
                            }
                        }                    
                    }
                    $attribute->setOptions($FieldNameOptions);
                    $attribute->setAction($actionName);
                    $attribute->loadAttributeTemplate($attributeType,$FieldName,$actionName);
                }
                catch (Exception $ex)
                {                
                    Core::Log(__METHOD__.$ex->getMessage(),"exception.log");
                }
                catch (ErrorException $ex)
                {
                    Core::Log(__METHOD__.$ex->getMessage(),"exception.log");
                }
            }
            return true;
        } 
        public function addFilter()
        {   
            $this->_whereCon=null;
            if($this->_parentNode)
            {
                $this->_whereCon=$this->_parentColName."='".$this->_parentSelector."'";
            }
            $requestedData=$this->_requestedData;           
            if(count($this->_showAttributes)>0)
            {
                foreach ($this->_showAttributes as $FieldName) 
                {
                    if($requestedData[$FieldName]!="")
                    {
                        if($this->_whereCon!="")
                        {
                            $this->_whereCon.=" and ";
                        }
                        if(Core::keyInArray($FieldName, $this->_nodeMTORelations))
                        {
                            $this->_conditionMTO[$FieldName]=$this->_nodeMTORelations[$FieldName];
                            $parentNodeDetails=new Core_Model_NodeProperties();
                            $parentNodeDetails->setNode($this->_nodeMTORelations[$FieldName]);
                            $parentStructure=$parentNodeDetails->currentNodeStructure();    
                            $relationNodeDR=$parentStructure['descriptor'];
                            $nr=new Core_Model_NodeRelations();
                            $nr->setNode($this->_nodeMTORelations[$FieldName]);                            
                            $nodeRelations=$nr->getCurrentNodeRelation();
                            if(Core::keyInArray($relationNodeDR, $nodeRelations))
                            {                
                                $np=new Core_Model_NodeProperties();
                                $np->setNode($nodeRelations[$relationNodeDR]);
                                $parentNodeStructure=$np->currentNodeStructure();
                                $this->_whereCon.=$FieldName.$relationNodeDR.".".$parentNodeStructure['descriptor']." like '%".$requestedData[$FieldName]."%'";
                                
                            }
                            else
                            {
                                $this->_whereCon.=$FieldName.".".$parentStructure['descriptor']." like '%".$requestedData[$FieldName]."%'";
                            }
                        }
                        else
                        {
                            $this->_whereCon.=$this->_nodeName.".".$FieldName." like '%".$requestedData[$FieldName]."%'";
                        }
                        
                    }
                }
            }            
        }
        public function defaultOnchangeEvents($FieldName)
        {
            $np=new Core_Model_NodeProperties();
            $np->setNode($this->_nodeName);
            $dependencyDetails=$np->setRelationDependency();
            $eventmethods=array();
            if(count($dependencyDetails)>0)
            {
                if(Core::keyInArray($FieldName, $dependencyDetails))
                {
                    $attributelist=Core::covertStringToArray($dependencyDetails[$FieldName],"|");
                    $nodeMTORelations=$this->_nodeMTORelations;
                    $onchange="";
                    foreach ($attributelist as $childColName) 
                    {
                        $onchange.="defaultphpfile('".$this->_nodeName."','".$this->_currentAction."','".$nodeMTORelations[$childColName]."','".$childColName."');";
                    }                    
                    $eventmethods[$FieldName]=$onchange;
                    
                }                
            }
            return $eventmethods;
            
        }
        public function defaultDependeeFilter($FieldName)
        {
            $np=new Core_Model_NodeProperties();
            $np->setNode($this->_nodeName);
            $dependencyDetails=$np->setRelationDependency();
            $FilterDependency=array();
            $depentparentList=array();
            if(count($dependencyDetails)>0)
            {
                foreach ($dependencyDetails as $parentColName => $dependentData) 
                {
                    $attributelist=Core::covertStringToArray($dependentData,"|");
                    foreach ($attributelist as $childColName) 
                    {
                        if($FilterDependency[$childColName])
                        {
                            $FilterDependency[$childColName]=$FilterDependency[$childColName]."|".$parentColName;
                        }
                        else 
                        {
                            $FilterDependency[$childColName]=$parentColName;
                        }                        
                    }
                }
            }
            $methodName=Core::covertStringToMethod($this->_nodeName."_".$FieldName."_addDescriptorFilter");
            if(Core::methodExists($this,$methodName))
            {
                $this->$methodName();
            }
            else
            {
                if($FilterDependency[$FieldName])
                {
                    $depentparentList=Core::covertStringToArray($FilterDependency[$FieldName]);                    
                }
            }
            return $depentparentList;
        }
        public function coreNodeSettingsOnchange()
        {
            $events=array();
            $events['tablename']="getPrimarykey();getAutokey();getNodeStructure();";           
            return $events;
        }
        
    }
?>
