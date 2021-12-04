<?php 
function dumping($test){
    echo "<pre style='color: red; font-weight:bold;'>"; 
    var_dump($test); 
    echo "</pre>"; 
}
function multi_echo()
{
    echo "<div style='color: red; font-weight:bold;'>";
    foreach (func_get_args() as $value)
    {
        echo $value."<br>"; 
    } 
    echo '</div>';
}

?>