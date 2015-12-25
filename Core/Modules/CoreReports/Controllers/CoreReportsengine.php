<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CoreReportsengine
 *
 * @author ramesh
 */
class Core_Modules_CoreReports_Controllers_CoreReportsengine extends Core_Controllers_NodeController
{
    //put your code here
    public function adminAction($param) 
    {
        $this->loadLayout("reportengine.phtml");
    }
}
