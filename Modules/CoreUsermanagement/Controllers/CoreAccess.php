<?php
class Modules_CoreUsermanagement_Controllers_CoreAccess extends Core_Controllers_NodeController
{
    public $_profileAccess;
    function adminRefreshAction()
    {
        $this->setProfileAccess();     
        $this->loadLayout("profileaccess.phtml");           
        parent::adminRefreshAction();
    }
    protected function setProfileAccess()
    {
        global $currentProfileCode;
        $this->_profileAccess=new Core_Attributes_BuildMenu();
        $this->_profileAccess->buildMenu();        
    }
}