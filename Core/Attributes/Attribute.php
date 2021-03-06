<?php
    class Core_Attributes_Attribute extends Core_Pages_PageLayout
    {
        public $_nodeName=NULL;
        public $_pkName=NULL;
        public $_onchange=NULL;
        public $_keyUp=NULL;
        public $_idName=NULL;
        public $_Value=NULL;
        public $_required=NULL;
        public $_readonly=NULL;
        public $_options=array();
        public $_action=NULL;
        public $_record=array();
        public $_multiedit=0;
        
        public function setNodeName($nodeName)
        {
            $this->_nodeName=$nodeName;
        }
        public function setPkName($pkName)
        {
            $this->_pkName=$pkName;
        }
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
            $tempValue=$value;
            if($tempValue)
            {
                $tempValue_list=Core::covertStringToArray($tempValue, "|");
                if(count($tempValue_list)>1)
                {
                    $tempValue=$tempValue_list;
                }
            }
            $this->_Value=$tempValue;
            
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
        function setRecord($record)
        {
            $this->_record=$record;
        }
        function setMultiEdit()
        {
            $this->_multiedit=1;
        }
        
        public function setOptions($result)
        {
            $this->_options=$result;
        }
        public function setOnchange($param) 
        {
            $this->_onchange.=$param;
        }
        
    }
?>