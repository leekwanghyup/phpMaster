<?php 
if(isset($_COOKIE['test'])){
    echo "쿠기 설정되어있음 !!!<br>";
    echo "쿠키 키 : {$_COOKIE['test']}"; 
} else {
    setcookie('test', 'TEST9823', time()+10);
}
?>
