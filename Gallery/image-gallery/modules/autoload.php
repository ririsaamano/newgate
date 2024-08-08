<?php

function my_autoload($Class) {
    require("modules/" . $Class . ".Class.php");
}

spl_autoload_register('my_autoload');

?>
