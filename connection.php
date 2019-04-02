<?php
class Connection
{
    var $connect;
    function __construct()
    {
        $this->connect = new mysqli('localhost','root','390b118bd06cbe7a',"vsadmin_viaspot");
    }
    function insert()
    {
        
    }
    function __destruct()
    {
        $this->connect->close;
    }
}
class Foo{
    var $connect;
    public function getSimple(){
      return $this; //the array cast is to force a stdClass result
    }
}
$bar = new Foo();
var_dump($bar->getSimple());
?>