<?php
    class Core_Cache_Refresh 
    {
        
        public function refreshCache()
        {
            $this->nodeActions();
            $this->nodeFiles();
            $this->nodeStructure();
            $this->adminThemeSettings();
            $this->profilePrivileges();
            $this->setLables();
            $this->nodeActions();
            $this->actionTypeList();
            $this->setRelations();
        }

        public  function nodeStructure()
        {
            $wb=new Core_WebsiteSettings();
            
            $folderpath=$wb->documentRoot.'var/'.$wb->identity;					    
            if (!file_exists($folderpath))
            {
                    mkdir($folderpath, 0755, true);
            }
            $qry=new Core_DataBase_ProcessQuery();
            $qry->setTable("core_node_settings","cns");
            $result=$qry->getRows("core_registernode_id");
            $fp=fopen($folderpath."/nodestructure.json","w+");
            fwrite($fp,  json_encode($result));
            fclose($fp);
            return ;
        }
        public function nodeFiles()
        {
            $wb=new Core_WebsiteSettings();
            
            $folderpath=$wb->documentRoot.'var/'.$wb->identity;					    
            if (!file_exists($folderpath))
            {
                    mkdir($folderpath, 0755, true);
            }
            $qry=new Core_DataBase_ProcessQuery();
            $qry->setTable("core_registernode","rn");
            $qry->addFieldArray(array("nodename"=>"nodename","nodefile"=>"nodefile","core_root_module_id"=>"rootmodule","core_module_id"=>"module","core_module_display_id"=>"moduledisplay"));            
            $result=$qry->getRows("nodename");
            $fp=fopen($folderpath."/nodefiles.json","w+");
            fwrite($fp,  json_encode($result));
            fclose($fp);
            return ;
        }
        public function profilePrivileges()
        {
            $wb=new Core_WebsiteSettings();
            $qry=new Core_DataBase_ProcessQuery();
            $qry->setTable("core_registernode", "rnd");
            $qry->addField("rnd.nodename
				,rnd.icon
				,rnd.menu
				,rnd.is_module
                                ,rnd.displayvalue
				,if(rnd.core_module_id is null,rnd.nodename,rnd.core_module_id) as core_module_id
				,if(rnd.core_module_display_id is null,rnd.nodename,rnd.core_module_display_id) as core_module_display_id
				,if(rnd.core_root_module_id is null,rnd.nodename,rnd.core_root_module_id) as core_root_module_id
				,group_concat(nac.short_code ORDER BY nac.sort_no ASC separator ',') as core_node_actions_id
				,group_concat(concat(nac.name,'||',nac.short_code) ORDER BY nac.sort_no ASC separator ',') as action_name_code");
            $qry->addJoin("nodename", "core_node_settings", "ns", "rnd.nodename=ns.core_registernode_id");
            $qry->addJoin("core_node_actions_id", "core_node_actions", "nac", " ns.core_node_actions_id like concat('%','|',nac.short_code,'|','%') 
		|| ns.core_node_actions_id like concat(nac.short_code,'|','%') 
		|| ns.core_node_actions_id like concat('%','|',nac.short_code) 
		|| (ns.core_node_actions_id=nac.short_code) ");
            $qry->addGroupBy("rnd.nodename");
            $qry->addOrderBy("rnd.is_module DESC ,rnd.sort_value ASC");
            $result=$qry->getRows("nodename");
            $totalResult['ROOT']=$result;
            
            $qry=new Core_DataBase_ProcessQuery();
            $qry->setTable("core_profile");            
            $profileDetails=$qry->getRows();
            if(count($profileDetails)>0)
            {
                foreach ($profileDetails as $pd)
                {
                    $qry=new Core_DataBase_ProcessQuery();
                    $qry->setTable("core_registernode", "rnd");
                    $qry->addField("rnd.nodename
                                        ,rnd.icon
                                        ,rnd.menu
                                        ,rnd.is_module
                                        ,rnd.displayvalue
                                        ,if(rnd.core_module_id is null,rnd.nodename,rnd.core_module_id) as core_module_id
                                        ,if(rnd.core_module_display_id is null,rnd.nodename,rnd.core_module_display_id) as core_module_display_id
                                        ,if(rnd.core_root_module_id is null,rnd.nodename,rnd.core_root_module_id) as core_root_module_id
                                        ,group_concat(nac.short_code ORDER BY nac.sort_no ASC separator ',') as core_node_actions_id
                                        ,group_concat(concat(nac.name,'||',nac.short_code) ORDER BY nac.sort_no ASC separator ',') as action_name_code");
                    $qry->addJoin("nodename", "core_node_settings", "ns", "rnd.nodename=ns.core_registernode_id");
                    $qry->addJoin("core_node_actions_id", "core_node_actions", "nac", " ns.core_node_actions_id like concat('%','|',nac.short_code,'|','%') 
                        || ns.core_node_actions_id like concat(nac.short_code,'|','%') 
                        || ns.core_node_actions_id like concat('%','|',nac.short_code) 
                        || (ns.core_node_actions_id=nac.short_code) ");
                    $qry->addJoin("core_profile_id", "core_access", "urac", " urac.action like concat('%',nac.short_code,'%') and urac.node=rnd.nodename ");
                    $qry->addWhere("core_profile_id='".$pd['short_code']."' || is_module='1'");
                    $qry->addGroupBy("rnd.nodename");
                    $qry->addOrderBy("rnd.is_module DESC ,rnd.sort_value ASC");
                    $result=$qry->getRows("nodename");
                    
                    $totalResult[$pd['short_code']]=$result;                 
                   
                }
            }
            $folderpath=$wb->documentRoot.'var/'.$wb->identity;					    
            if (!file_exists($folderpath))
            {
                    mkdir($folderpath, 0755, true);
            }
            $fp=fopen($folderpath."/profileacess.json","w+");
            fwrite($fp,  json_encode($totalResult));
            fclose($fp);
            return ;
        }
        public function nodeActions()
        {
            $wb=new Core_WebsiteSettings();
            $qry=new Core_DataBase_ProcessQuery();
            $qry->setTable("core_node_actions");
            $qry->addFieldArray(array("lower(short_code)"=>"short_code","name"=>"name"));
            $result=$qry->getRows("short_code", "name");
            $folderpath=$wb->documentRoot.'var/'.$wb->identity;					    
            if (!file_exists($folderpath))
            {
                    mkdir($folderpath, 0755, true);
            }
            $fp=fopen($folderpath."/nodeactions.json","w+");
            fwrite($fp,  json_encode($result));
            fclose($fp);
            return ;
             
        }
        public function adminThemeSettings()
        {
            $wb=new Core_WebsiteSettings();
            $qry=new Core_DataBase_ProcessQuery();
            $qry->setTable("core_adminthemesettings");
            $qry->addField("*");
            $result=$qry->getRow();
            $folderpath=$wb->documentRoot.'var/'.$wb->identity;					    
            if (!file_exists($folderpath))
            {
                    mkdir($folderpath, 0755, true);
            }
            $fp=fopen($folderpath."/admintheme.json","w+");
            fwrite($fp,  json_encode($result));
            fclose($fp);
            return ;
        }
        public function actionTypeList()
        {
            $wb=new Core_WebsiteSettings();
            $qry=new Core_DataBase_ProcessQuery();
            $qry->setTable("core_node_actions");
            $qry->addFieldArray(array("short_code"=>"short_code","core_action_type_id"=>"core_action_type_id"));
            $result=$qry->getRows("short_code","core_action_type_id");
            $folderpath=$wb->documentRoot.'var/'.$wb->identity;					    
            if (!file_exists($folderpath))
            {
                    mkdir($folderpath, 0755, true);
            }
            $fp=fopen($folderpath."/actiontype.json","w+");
            fwrite($fp,  json_encode($result));
            fclose($fp);
            return ;
        }
        public function setLables()
        {
            $wb=new Core_WebsiteSettings();
            $qry=new Core_DataBase_ProcessQuery();
            $qry->setTable("core_registernode");
            $qry->addFieldArray(array("nodename"=>"nodename","displayvalue"=>"displayvalue"));            
            $noderesult=$qry->getRows("nodename","displayvalue");
            $qry=new Core_DataBase_ProcessQuery();
            $qry->setTable("core_label_details");
            $qry->addFieldArray(array("original_name"=>"original_name","display_name"=>"display_name"));            
            $result=$qry->getRows("original_name","display_name");
            $result+=$noderesult;
            $folderpath=$wb->documentRoot.'var/'.$wb->identity;					    
            if (!file_exists($folderpath))
            {
                    mkdir($folderpath, 0755, true);
            }
            $fp=fopen($folderpath."/language.json","w+");
            fwrite($fp,  json_encode($result));
            fclose($fp);
            return ;
        }
        public function setRelations()
        {
            $wb=new Core_WebsiteSettings();
            $qry=new Core_DataBase_ProcessQuery();
            $qry->setTable("core_node_relations");
            $qry->addFieldArray(array("core_node_settings_id"=>"node","core_node_colname"=>"colname","core_node_parent"=>"parent"));
            $noderesult=$qry->getRows();
            $finalresult=array();
            if(count($noderesult)>0)
            {
                foreach($noderesult as $nodeRelation)
                {
                    $finalresult[$nodeRelation['node']][$nodeRelation['colname']]=$nodeRelation['parent'];
                }
            }
            $folderpath=$wb->documentRoot.'var/'.$wb->identity;					    
            if (!file_exists($folderpath))
            {
                    mkdir($folderpath, 0755, true);
            }
            $fp=fopen($folderpath."/noderelations.json","w+");
            fwrite($fp,  json_encode($finalresult));
            fclose($fp);
            return ;
        }
    }
?>