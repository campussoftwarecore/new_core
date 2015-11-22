<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of HeaderPage
 *
 * @author ramesh
 */
class Core_Pages_HeaderPage extends Core_Pages_PageLayout
{
    public function __construct()
    {
        $this->buildHeader(); 
       
    }
    protected function buildHeader()
    {
        
        $this->loadLayout("header.phtml");
    }    
}
