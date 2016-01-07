<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CoreUsedashboard
 *
 * @author ramesh
 */
class Core_Modules_CoreDashboard_Controllers_CoreUsedashboard extends Core_Controllers_NodeController
{
    //put your code here
    public function adminAction() 
    {
        $this->loadLayout("usedashboard.phtml");
    }
}
