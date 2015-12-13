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
        
        public function __construct() 
        {
            
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
            $this->getTotalResultCount();
            $db=new Core_DataBase_ProcessQuery();            
            $db->setTable($this->_tableName);            
            if(count($this->_showAttributes)>0)
            {
                $db->addField("id");
                foreach ($this->_showAttributes as $fieldName) 
                {
                    if(key_exists($fieldName, $this->_nodeRelations))
                    {
                        $relationNode=  $this->_nodeRelations[$fieldName];                        
                        $np=new Core_Model_NodeProperties($relationNode);
                        $relationNodeStructure=$np->currentNodeStructure();                        
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
                            $db->addFieldArray(array($fieldName.".".$relationNodeDR=>$fieldName));
                        }
                    }
                    else
                    {
                        $db->addField($fieldName);
                    }
                }                
            }
            $ws=new Core_WebsiteSettings();
            $rpp=$ws->rpp;
            $page=1;
            if($this->_requestedData['rpp_'.$this->_nodeName])
            {
                $rpp=$this->_requestedData['rpp_'.$this->_nodeName];
            }
            $this->_rpp=$rpp;
            $this->_wsrpp=$ws->rpp;
            if($this->_requestedData['page_'.$this->_nodeName])
            {
                $page=$this->_requestedData['page_'.$this->_nodeName];
            }
            $this->_page=$page;
            $this->addFilter();
            $db->addWhere($this->_whereCon);
            $db->addGroupBy("id");
            $db->addOrderBy("id DESC");
            $db->setLimit(($page-1)*$this->_rpp,$this->_rpp);     
            $db->buildSelect();                   
            $this->_collections=$db->getRows($this->_primaryKey);             
        }
        public function getTotalResultCount()
        {
            $db=new Core_DataBase_ProcessQuery();            
            $db->setTable($this->_tableName); 
            $this->addFilter();
            $db->addFieldArray(array("count(distinct(".$this->_tableName.".id))"=>"count"));
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
        public function loadAttribute($FieldName)
        {            
            
            $mandotatoryAttributes=$this->mandotatoryAttributes();			
            $readonlyAttributes=$this->readonlyAttributes();            
            
            $onchangeEvents=array();
            $eventmethod=lcfirst(str_replace(" ","",ucwords(str_replace("_", " ",$this->_nodeName)))."Onchange");
            if(Core::methodExists($this, $eventmethod))
            {
                $onchangeEvents=$this->$eventmethod();
            }            
            $methodName=$FieldName."_loadAttribute";
            if(Core::methodExists($this,$methodName))
            {
                $this->$methodName();
            }
            
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
                $attributeDetails=new Core_Attributes_LoadAttribute($attributeType);				
				$attributeClass=Core_Attributes_.$attributeDetails->_attributeName;
				$attribute=new $attributeClass;
				
                $attribute->setIdName($FieldName);
                $attribute->setValue($this->_record[$FieldName]);
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
                $attribute->setAction($this->_currentAction);
                $attribute->loadAttributeTemplate($attributeType,$FieldName);
            }
            catch (Exception $ex)
            {
                
                echo $ex->getMessage();
            }
            catch (ErrorException $ex)
            {
                echo $ex->getTraceAsString();
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
                        $this->_whereCon.=$FieldName." like '%".$requestedData[$FieldName]."%'";
                    }
                }
            }
            
        }
        public function coreNodeSettingsOnchange()
        {
            $events=array();
            $events['tablename']="getPrimarykey();getNodeStructure();";           
            return $events;
        }
        
    }
?>
