<?php
    class Core_DataBase_DbConnect 
    { 
        protected   $default;          // The MySQL database connection 
        public      $output=array();
        /* Class constructor */ 
        function __construct()
        {        
           $this->default = mysqli_connect("localhost", "root",'',"new_easy_core") or die("Please Check DB");            
        } 

        function begin()
        { 
       
            $null = mysqli_query("START TRANSACTION", $this->default); 
            return mysqli_query("BEGIN", $this->default); 
        } 

        function commit()
        { 
           return mysqli_query("COMMIT", $this->default); 
        } 

        function rollback()
        { 

           return mysqli_query("ROLLBACK", $this->default); 
        } 

        function executeQuery($query)
        { 
            
            try
            {
                $this->output['result'] = mysqli_query($this->default,$query) or die($query); 
                $this->output['affetedrows'] =mysqli_num_rows($this->output['result']);
                $this->output['affetedfields']=mysqli_num_fields($this->output['result']);
                return $this->output;         
            }
            catch(Exception $ex)
            {
                echo $ex->getMessage();
            }
        }
    }
?>