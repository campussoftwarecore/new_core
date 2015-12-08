<?php
    class Core_Attributes_BuildMenu 
    {
        public $_accessbleNode=array();
        public $_rootModules=array();
        public $_moduleDisplay=array();
        public $_nodeDisplay=array();
        public $_menuItems=array();
        public $_moduleList=array();
        public $_nodeLink=array();
        public function __construct() 
        {
            global $currentProfileCode;
            $np=new Core_Model_NodeProperties();
            $this->_accessbleNode=$np->getCurrentProfilePermission($currentProfileCode);            
        }       
        public function buildMenu()
        {
            $wp=new Core_WebsiteSettings();
            if(count($this->_accessbleNode)>0)
            {
                $k=0;                
                foreach ($this->_accessbleNode as $nodeData)
                {
                    if($nodeData['core_module_id']!="" && $nodeData['menu']==1) 
                    {
                            $this->_menuItems[$nodeData['core_root_module_id']][$nodeData['core_module_display_id']][$nodeData['core_module_id']][$nodeData['nodename']]=$nodeData['core_node_actions_id'];
                            $this->_moduleList[$nodeData['core_root_module_id']]=$nodeData['core_root_module_id'];
                            $this->_nodeLink[$nodeData['nodename']]=$wp->websiteAdminUrl.$nodeData['nodename'];
                    }
                    $this->_nodeDisplay[$nodeData['nodename']]=$nodeData['displayvalue'];                    
                    if($nodeData['is_module']=='1' && ($nodeData['core_root_module_id']=="" || $nodeData['nodename']==$nodeData['core_root_module_id']))
                    {
                            $this->_rootModules[$k]=$nodeData;
                            $k++;
                    }                    
                }                
            } 
        }
    }
?>