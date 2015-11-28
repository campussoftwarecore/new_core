<?php
class Core_Controllers_NodeController extends Core_Model_Node
{
    public $_requestedData=array();
    public $_filesData=array();
    public $_nodeName=NULL;
    public $_currentAction="Admin";
    public $_websiteAdminUrl=NULL;
    public $_methodType=NULL;
            
    function __construct($nodeName,$action) 
    {
        $wp=new Core_WebsiteSettings();                
        $this->setNodeName($nodeName);
        $this->setActionName($action);
        $this->_websiteHostUrl=$wp->websiteAdminUrl.$this->getNodeName()."/";
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
    public function addAction()
    {
        $requestedData=$this->_requestedData;
        if($this->_methodType=="REQUEST")
        {
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
                $nodeSave=new Core_Model_NodeSave();
                $nodeSave->setNode($this->_nodeName);            
                foreach($this->_showAttributes as $FieldName)
                {                
                    $nodeSave->setData($FieldName,$requestedData[$FieldName]);
                } 
                $nodeSave->save();
                $output=array();
                $output['status']="success";
                $output['redirecturl']=$this->_websiteHostUrl;            
                echo json_encode($output);
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
        
        if($this->_methodType=="REQUEST")
        {
            
            $this->getRecordLoad();
            $loadResponse=$this->loadLayout("editform.phtml");
            if($loadResponse==false)
            {
                $loadResponse=$this->loadLayout("form.phtml");
            }
        }
        else
        {
            $nodeSave=new Core_Model_NodeSave();
            $nodeSave->setNode($this->_nodeName);
            $nodeSave->setData("id",$requestedData["id"]);
            foreach($this->_showAttributes as $FieldName)
            {                
                $nodeSave->setData($FieldName,$requestedData[$FieldName]);
            } 
            $nodeSave->save();        
            $output=array();
            $output['status']="success";
            $output['redirecturl']=$this->_websiteHostUrl;            
            echo json_encode($output);
        }
        
    }
    public function viewAction()
    {
        if($this->_methodType=="REQUEST")
        {
            
            $this->getRecordLoad();
            $loadResponse=$this->loadLayout("viewform.phtml");
            if($loadResponse==false)
            {
                $loadResponse=$this->loadLayout("form.phtml");
            }
        }        
        
    }
    public function deleteAction()
    {
        
        $nodeDelete=new Core_Model_NodeDelete();
        $nodeDelete->setNode($this->_nodeName);
        $nodeDelete->addFilterCondition("(".$this->_tableName.".".$this->_primaryKey." = '".$this->_currentSelector."'".")");           
        $nodeDelete->delete();
        $output=array();
        $output['status']="success";
        $output['redirecturl']=$this->_websiteHostUrl;         
        if($this->_methodType=='REQUEST')
        {
            Core::redirectUrl($this->_websiteHostUrl);
        }
        else
        {
            echo json_encode($output);
        }
        return;
    }
    public function descriptorAction()
    {
        $rquestedData=$this->_requestedData;
        $sourceNode=$this->_requestedData['node'];
        $DestinationNode=$this->_requestedData['destinationNode'];         
        $FieldName=$this->_requestedData['idname'];  
	$noderesult=$this->_requestedData['noderesult'];
        if($noderesult!="")
        {
            $noderesult=  json_decode($noderesult,true);
        }
        else
        {
            $noderesult=array();
        }
        $defaultValue=$noderesult[$FieldName];
        $readonlyAttributes=$this->readonlyAttributes($rquestedData['action']);
        $np=new Core_Model_NodeProperties();
        $np->setNode($sourceNode);
        $sourceNodeStructure=$np->currentNodeStructure();
        if(Core::isArray($sourceNodeStructure))
        {            
            $readonlyAttributes=Core::covertStringToArray($sourceNodeStructure['readonly_'.$rquestedData['action']]);   
            $mandotatoryAttributes=Core::covertStringToArray($sourceNodeStructure['mandotatory_'.$rquestedData['action']]);   
        }
        
		
        $db=new Core_DataBase_ProcessQuery();
        $db->setTable($this->_tableName, $this->_nodeName);
        $db->addFieldArray(array($this->_nodeName.".".$this->_primaryKey=>"pid"));
        
        if(in_array($this->_descriptor,$this->_nodeRelations))
        {
            
        }
        else 
        {
            $db->addFieldArray(array($this->_nodeName.".".$this->_descriptor=>"pds"));
        }
        if(in_array($FieldName,$readonlyAttributes) || $rquestedData['action']=='view')
        {
            $db->addWhere($this->_nodeName.".".$this->_primaryKey."='".$defaultValue."'");
	}        
        $db->addOrderBy($this->_descriptor);
        $result=$db->getRows();        
        try
        {            
            
            $attributeType="select";
            $attributeDetails=new Core_Attributes_LoadAttribute($attributeType);				
            $attributeClass=Core_Attributes_.$attributeDetails->_attributeName;
            $attribute=new $attributeClass;
            $attribute->setIdName($this->_requestedData['idname']);
            $attribute->setOptions($result);
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
        catch (Exception $ex)
        {
            echo $ex->getMessage();
        }
        
    }

    public function gridContent()
    {        
        $this->setSingleActions();  
        $this->setIndividualActions();
        $this->getCollection();
        $this->loadLayout("maingrid.phtml");
    }    
    public function adminRefreshAction()
    {
        
        $this->setSingleActions();  
        $this->setIndividualActions();
        $this->getCollection();
        $this->loadLayout("grid.phtml");
    }

    public function setSingleActions()
    {        
        return parent::setSingleActions();
    }
}
?>