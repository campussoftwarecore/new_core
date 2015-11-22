<?php

    class Core_Pages_NavigationPage extends Core_Pages_PageLayout
    {
        public $_navigationPath=Array();
        public function __construct()
        {
            $fileName="navigation.phtml";          
            $this->loadLayout($fileName);
            
        } 
        public function getNavigationPath()
        {
            global $currentNode;
            $wp=new Core_WebsiteSettings();
            $lb=new Core_Model_Language(); 
            if($currentNode)
            {
                $this->_navigationPath[0]['label']=$lb->getLabel($currentNode);
                $this->_navigationPath[0]['url']=$wp->websiteUrl.$currentNode;
            }
            return $this->_navigationPath;
            
        }
    }
?>