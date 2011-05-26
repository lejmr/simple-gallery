<?php
require_once 'vendors/limonade/lib/limonade.php';


dispatch('/', 'hello');
  function hello()
  {
      return 'Hello world!';
  }
run();

?>