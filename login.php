<?php
    unset($_SESSION);
    error_reporting(0);
    include_once 'Boostrap.php';
    $page=new Core_Pages_PageLayout();
    $page->loadLayout("login.phtml");
?>