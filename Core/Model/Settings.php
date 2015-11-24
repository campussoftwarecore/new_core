<?php
public class Core_Model_Settings
{
    protected $_nodeName=NULL;
    function __construct($nodeName=NULL) 
    {
        $this->_nodeName=$nodeName;
    }
    public function setNode($nodeName)
    {
        $this->_nodeName=$nodeName;
    }
    public function getTableStructure()
    {
        
    }
}
?>