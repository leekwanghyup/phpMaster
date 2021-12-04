<?php 
    $userid = $_REQUEST['userid'] ?? '';
    $username = $_REQUEST['username'] ?? '';
?>

<form action="proceed.php" method="post">
<div> 아이디 : <?php echo $userid ?> </div>
<div> 이름 : <?php echo $username ?> </div>
<div>
    <input type="submit" name='confirm' value="확인">
    <input type="button" class="back" value="돌아가기">
    <input type="hidden" name="userid" value="<?= $userid ?>">
</div>
</form>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
    $('.back').on('click',function(){
        window.history.back();
    })
</script>