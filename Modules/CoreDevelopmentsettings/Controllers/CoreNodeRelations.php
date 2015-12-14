<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CoreNodeRelations
 *
 * @author ramesh
 */
class Modules_CoreDevelopmentsettings_Controllers_CoreNodeRelations extends Core_Controllers_NodeController
{
    public function coreNodeRelationsAfterDataUpdate()
    {
        $cache=new Core_Cache_Refresh();
        $cache->setRelations();                                                 
        return TRUE;  
    }
}
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  