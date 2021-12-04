> 테스트에서 사용할 라이브러리
```php
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
```
<br>

# SECTION 35 텍스트 송신 
- 이 예제에서 사용할 패턴은 다음과같다. 
- index.php에서 데이터를 송신하여
- proceed.php 수신 후 데이터를 처리한다. 
> index.php
```php
 # form 태그 
```
> proceed.php
```php
 # 데이터 처리
```

<br>

## 데이터 전송 및 수신
> index.php
```html
<form action="proceed.php" method="post">
    <input type="text" name="username">
    <button>전송</button>
</form>
```
> proceed.php
```php
require_once 'lib.php';
$username = $_POST['username'] ?? ''; 
$username_req = $_REQUEST['username'] ?? '';
    # POST방식 전송인 경우 $_POST
    # GET방식 전송인 경우 $_GET
    # POST,GET 여부와 관계없이 $_REQEUST를 사용할 수 있다.

multi_echo($username, $username_req); 
```

<br>

# SECTION 36 여러행의 텍스트 

## 개행이 된 텍스트 입력한대로 표시하기 
> index.php
```html
<form action="proceed.php" method="post">
    <textarea name="contents" id="" cols="30" rows="15"></textarea>
    <div><button>전송</button></div>
</form>
```
> proceed.php
```php
require_once 'lib.php';
$contets = $_POST['contents'] ?? ''; 
echo nl2br($contets);
```
- textarea에 개행이 들어간 문장을 적고 전송해보자
- 개행된 상태로 출력된 것을 알 수 있다. 

<br>
<br>

# SECTION 37 hidden 태그의 데이터
> index.php
```html
<form action="proceed.php" method="post">
    <input type="hidden" name="userid" value="001"/>
    <div>
        <input type="text" name="username"/>
    </div>
    <div>
        <button type="submit">전송</button>
    </div>
</form>
```
> proceed.php
```php
<?php
require_once 'lib.php';
# 글로벌 변수 $_POST, $_GET, $_REQUEST 받을 수 있다. 
$userid = $_REQUEST['userid'] ?? '';
$username = $_REQUEST['username'] ?? '';

multi_echo($userid, $username);

# 다음과 같이 input:hidden 태그에 저장할 수 있다.
?> 
<input type="hidden" value="<?= $userid ?>">
```

<br>
<br>

# SECTION 38 송신버튼
> index.html
```PHP
<form action="confirm.php" method="post">
    <input type="hidden" name="userid" value="001"/>
    <div>
        <input type="text" name="username"/>
    </div>
    <div>
        <button type="submit">전송</button>
    </div>
</form>
```

> confirm.php
```PHP
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
```

>proceed.php
```PHP
require_once 'lib.php';

if(isset($_REQUEST['back'])){
    header("Location : /index.php");
} else {
    echo "전송처리";
}
```

<br>

# SECTION 39 체크박스 데이터 받기 
- 여러개의 버튼을 다중 선택할 수있다. 
- input태그의 name속성이름을 배열형식으로 설정한다. 
- 예를 들면 다음과 같이 설정 
    + name="vegetable[]"
> index.php
```PHP
<form action="proceed.php" method="post">
    <label for="vege1">다마네기</label>
    <input type="checkbox" name="vegetable[]" value="다마네기" id="vege1">
    <label for="vege2">당근</label>
    <input type="checkbox" name="vegetable[]" value="당근" id="vege2">
    <label for="vege3">시금치</label>
    <input type="checkbox" name="vegetable[]" value="시금치" id="vege3">
    <label for="vege4">가랑파</label>
    <input type="checkbox" name="vegetable[]" value="가랑파" id="vege4">
    <button>전송</button>
</form>
```

> proceed.php
```PHP
require_once 'lib.php';

$vegetable = $_REQUEST['vegetable'] ?? '';
dumping($vegetable);
/*
체크한 값이 배열로 전달된다.
시금치와 가랑파를 선택한 경우 다음과 같은 배열이 만들어진다.
array(2) {
  [0]=>
  string(9) "시금치"
  [1]=>
  string(9) "가랑파"
}
*/
```

<br>

# SECTION 40 라디오 버튼
- 여러개의 버튼 중 하나만 선택할 수 있다. 
> index.php
```html
<form action="proceed.php" method="post">
    <div>
        <p>2022년 당신의 선택은 ?</p>
        <input type="radio" name="candidate" id="c001" value="이재명">
        <label for="c001">1. 이재명</label>
        
        <input type="radio" name="candidate" id="c002" value="윤석열">
        <label for="c002">2. 윤석열</label>

        <input type="radio" name="candidate" id="c003" value="안철수">
        <label for="c003">3. 안철수</label>
    </div>
    <div><button>전송</button></div>
</form>
```

> proceed.php
```PHP
require_once 'lib.php';

$candidate = $_REQUEST['candidate'] ?? '';
dumping($candidate);
# 체크한 데이터를 문자열로 받는다. 

/*
string(9) "이재명"
*/
```

<br>
<br>

# SECTION 41 풀다운 메뉴 
- 선택할 수 있는 항목이 하나이다.
> index.php
```html
<form action="proceed.php" method="post">
    <select name="prefecture">
        <option value="" selected>행정구역을 선택하세요</option>
        <option value="서울특별시">서울특별시</option>
        <option value="인천광역시">인천광역시</option>
        <option value="대전광역시">대전광역시</option>
        <option value="울산광역시">울산광역시</option>
        <option value="광주광역시">광주광역시</option>
        <option value="대구광역시">대구광역시</option>
        <option value="부산광역시">부산광역시</option>  
    </select>
    <button>전송</button>
</form>
```
> proceed.php
```php
require_once 'lib.php';
$prefecture = $_REQUEST['prefecture'] ?? '';
dumping($prefecture);
# 체크한 데이터를 문자열로 받는다. 
```

<br>
<br>

# SECTION 42 리스트 박스
- 항목을 다중 선택할 수 있다. 
- select 태그에 multiple 속성을 추가하고  name속성값은 배열을 나타내는 '[]' 사용한다. 
```html
<form action="proceed.php" method="post">
    <p>취미선택</p>
    <select name="hobby[]" multiple>
        <option value="독서">독서</option>
        <option value="영화감상">영화감상</option>
        <option value="영어회화">영어회화</option>
        <option value="음악감상">음악감상</option>
        <option value="노래">노래</option>
        <option value="원예">원예</option>
        <option value="사진">사진</option>
        <option value="드라이브">드라이브</option>
        <option value="골프">골프</option>
        <option value="서핑">서핑</option>
        <option value="여행">여행</option>
        <option value="낚시">낚시</option>
        <option value="요리">요리</option>
    </select>
    <div><button>전송</button></div>
</form>
```
> proceed.php
```php
require_once 'lib.php';

$hobby = $_REQUEST['hobby'] ?? '';
dumping($hobby);
# 선택한 데이터를 배열로 받는다. 
/*
array(4) {
  [0]=>
  string(12) "영화감상"
  [1]=>
  string(12) "영어회화"
  [2]=>
  string(6) "원예"
  [3]=>
  string(6) "사진"
}
*/
```

# SECTION 43 쿠키
```PHP
if(isset($_COOKIE['test'])){
    echo "쿠기 설정되어있음 !!!<br>";
    echo "쿠키 키 : {$_COOKIE['test']}"; 
} else {
    setcookie('test', 'TEST9823', time()+10);
}
```