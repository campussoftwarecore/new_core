<?php
    class Core_Attributes_Attribute extends Core_Pages_PageLayout
    {
        public $_onchange=NULL;
        public $_keyUp=NULL;
        public $_idName=NULL;
        public $_Value=NULL;
        public $_required=NULL;
        public $_readonly=NULL;
        public $_options=array();
        public $_action=NULL;
        
        
        public function setIdName($idName)
        {
            $this->_idName=$idName;
        }
        public function setAction($action)
        {
            $this->_action=$action;
        }
        public function setValue($value)
        {
            $this->_Value=$value;
        }
        public function setRequired()
        {
            $this->_required="required";
        }
        public function setReadonly()
        {			
            $this->_readonly=true;
        }
        function valiadte($action,$mode)
        {
            
        }
        public function setOptions($result)
        {
            $this->_options=$result;
        }
        
    }
?>