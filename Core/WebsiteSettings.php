<?php
    define("ADMINNAME", "admin");
    define("ADMINPASS", 'Ramesh');
    class Core_WebsiteSettings
    {
        public $websiteUrl=NULL;
        public $websiteAdminUrl=NULL;
        public $documentRoot=NULL;
        public $identity=NULL;
        public $themeName=NULL;        
        public $documentRootUpload=NULL;       
                
        function __construct() 
        {
            $dbConfig=Core::getSiteConfig(); 
            $this->websiteUrl="http://".$dbConfig['websitehost']."";
            $this->websiteAdminUrl="http://".$dbConfig['websitehostadmin']."";
            $this->documentRoot=$dbConfig['documentroot'];
            $this->identity="Core";
            $this->themeName="default";
            $this->rpp="17";
            $this->documentRootUpload="uploads/".$this->identity;
        }
    }
?>
