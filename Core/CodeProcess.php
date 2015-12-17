<?php
    class Core_CodeProcess 
    {
        public function stripslashes_deep($value)
	{
		$value = is_array($value) ?array_map('stripslashes_deep', $value) :stripslashes($value);
	
		return $value;
	}        
        public function convertEncryptDecrypt($action,$string)
        {
            $output = false;
	
	    $encrypt_method = "AES-256-CBC";
	    $secret_key = ENCRYPTION_KEY;
	    $secret_iv = ENCRYPTION_KEY;
	
	    // hash
	    $key = hash('sha256', $secret_key);
	    
	    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
	    $iv = substr(hash('sha256', $secret_iv), 0, 16);
	
	    if( $action == 'encrypt' ) 
            {
		$output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
		$output = base64_encode($output);
	    }
	    else if( $action == 'decrypt' )
            {
		$output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
	    }
	
	    return $output ;
        }
        public function dirToArray($dir) 
        { 
   
            $result = array();
            $cdir = scandir($dir); 
            foreach ($cdir as $key => $value) 
            { 
               if (!in_array($value,array(".",".."))) 
               { 
                  if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) 
                  { 
                     $result[$value] = $this->dirToArray($dir . DIRECTORY_SEPARATOR . $value); 
                  } 
                  else 
                  { 
                     $result[] = $value; 
                  } 
               } 
            } 

            return $result; 
        }
        function rmdir_recursive($dir) 
        {
            foreach(scandir($dir) as $file) 
            {
                if ('.' === $file || '..' === $file) continue;
                if (is_dir("$dir/$file")) 
                {
                    $this->rmdir_recursive("$dir/$file");
                }
                else 
                {
                    unlink("$dir/$file");
                }
            }
            rmdir($dir);
        }
        function createZipFile($path,$targetfilepath)
        {
            $zip_name="temfolder.zip";
            $zip = new ZipArchive();		
            if($zip->open($zip_name, ZIPARCHIVE::CREATE)!==TRUE)
            {
                    $error .= "* Sorry ZIP creation failed at this time";
            }
            $valid_files=$this->dirToArray($path);
            
            foreach($valid_files as $key=>$file)
            {
                    if(is_array($file))
                    {				
                            foreach($file as $subfile)
                            {
                                    $filepath=$path."/".$key."/".$subfile;
                                    if(file_exists($filepath))
                                    {
                                            $zip->addFile($filepath,$foldername."/".$key."/".$subfile);
                                    }
                            }
                    }
                    else
                    {
                            $filepath=$path."/".$file;
                            if(file_exists($filepath))
                            {
                                    $zip->addFile($filepath,$foldername."/".$file);
                            }
                    }

            }		 
            $zip->close();           
            rename($zip_name, $targetfilepath.".zip");
            return true;
        }
    }
?>
