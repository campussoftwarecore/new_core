<?php
    
    class Core_WebsiteSettings
    {
        public $websiteUrl=NULL;
        public $websiteAdminUrl=NULL;
        public $documentRoot=NULL;
        public $identity=NULL;
        public $themeName=NULL;
                
        function __construct() 
        {
            $this->websiteUrl="http://".$_SERVER['HTTP_HOST']."/new_core/";
            $this->websiteAdminUrl="http://".$_SERVER['HTTP_HOST']."/new_core/";
            $this->documentRoot=$_SERVER['DOCUMENT_ROOT']."new_core/";
            $this->identity="Core";
            $this->themeName="default";
            $this->rpp="10";
        }
    }
?>
