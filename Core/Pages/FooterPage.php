<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FooterPage
 *
 * @author ramesh
 */
class Core_Pages_FooterPage extends Core_Pages_PageLayout
{
    public function __construct()
    {
        
        $this->buildHeader(); 
       
    }
    protected function buildHeader()
    {
        $ws=new Core_WebsiteSettings();
        global $currentnode;
        if(file_exists($ws->documentRoot."pages/".$ws->themeName."/".$currentnode."/"."footer.phtml"))
        {
            $filename=$ws->documentRoot."pages/".$ws->themeName."/".$currentnode."/"."footer.phtml";
        }
        else if(file_exists($ws->documentRoot."pages/".$currentnode."/"."footer.phtml"))
        {
            $filename=$ws->documentRoot."pages/".$currentnode."/"."footer.phtml";
        }
        else if(file_exists($ws->documentRoot."pages/".$ws->themeName."/"."footer.phtml"))
        {
            $filename=$ws->documentRoot."pages/".$ws->themeName."/"."footer.phtml";
        }
        else if(file_exists($ws->documentRoot."pages/"."footer.phtml"))
        {
            $filename=$ws->documentRoot."pages/"."footer.phtml";
        }
        
        if(file_exists($filename))
        {
            include_once $filename;
        }
    }    
}