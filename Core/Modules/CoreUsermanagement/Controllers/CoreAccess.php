<?php
class Core_Modules_CoreUsermanagement_Controllers_CoreAccess extends Core_Controllers_NodeController
{
    public $_profileAccess;
    public $_existingRecord;
            
    function adminRefreshAction()
    {
        $this->setProfileAccess();     
        $this->loadLayout("profileaccess.phtml");           
        return true;
    }
    protected function setProfileAccess()
    {
        global $currentProfileCode;
        $db=new Core_DataBase_ProcessQuery();
        $db->setTable($this->_tableName);
        $db->addWhere($this->_parentColName."='".$this->_parentSelector."'");
        $this->_existingRecord=$db->getRows("node");        
        $this->_profileAccess=new Core_Attributes_BuildMenu();
        $this->_profileAccess->buildMenu();        
    }
    public function saveAction()
    {
        $db=new Core_DataBase_ProcessQuery();
        $db->setTable($this->_tableName);
        $db->addWhere($this->_parentColName."='".$this->_parentSelector."'");
        $db->buildDelete();
        $db->executeQuery();
        foreach($this->_requestedData as $key=>$data)
        {
            if(Core::isArray($data))
            {
                $db->setTable($this->_tableName);
                $db->addFieldArray(array("node"=>$key,"action"=>Core::covertArrayToString($data, "|"),$this->_parentColName=>$this->_parentSelector));
                $db->buildInsert();                
                $db->executeQuery();
            }
        }
        $cache=new Core_Cache_Refresh();        
        $cache->profilePrivileges();
        $backUrl=$this->_websiteAdminUrl.$this->_parentNode."/".$this->_parentAction."/".$this->_parentSelector;
        $output=array();
        $output['status']="success";
        $output['redirecturl']=$backUrl;            
        echo json_encode($output);
    }
}