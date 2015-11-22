<?php

class Core_FileList 
{
    protected $_filelist=array();
    protected $_directory=null;
    protected $_filterExtension=null;
    public function setDir($directoryName)
    {
        $this->_directory=$directoryName;
    }
    public function setFilterExtension($extension)
    {
        $this->_filterExtension=$extension;
    }
    public function scanFileList($directory=null)
    {
        if($directory==null)
        {
            $directory=  $this->_directory;
        }
        if(file_exists($directory))
        {
            $directoryfiles=  scandir($directory, 1);
            if(count($directoryfiles)>0)
            {
                foreach ($directoryfiles as $fpname)
                {
                    if($fpname!=".." && $fpname!=".")
                    {
                        $tempfpname=$directory."/".$fpname;
                        if(is_dir($tempfpname))
                        {
                            $this->_filelist=$this->scanFileList($tempfpname);
                        }
                        else
                        {
                            $pathinfo=new SplFileInfo($tempfpname);
                            $extenstion=$pathinfo->getExtension();
                            if($extenstion==$this->_filterExtension || $this->_filterExtension==NULL)
                            {
                                $this->_filelist[]=$tempfpname;
                            }
                            
                        }
                    }
                }
                
            }
        }
        return $this->_filelist;
    }
    //put your code here
}
