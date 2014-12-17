<?php 
if(!isset($_SERVER['HTTP_HOST'])){
    $_SERVER['HTTP_HOST']="localhost";
}
echo $_SERVER['HTTP_HOST'];

