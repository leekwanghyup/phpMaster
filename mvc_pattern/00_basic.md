## 디버깅 전용 라이브러리 
- test.lib.php 
- 테스트에 사용할 함수이다.
```php
function dumping($test){
    echo "<pre>"; 
    var_dump($test); 
    echo "</pre>"; 
    exit;
}
```

<br>

## 기본 문법
```php
# 변수값이 null인경우 ?? 이하 값이 할당된다.
echo $_POST['pram'] ?? '없음';
```

## 마법상수 
```php
echo __FILE__, "<br>"; #  C:\xampp\htdocs\mvc\index.php
echo dirname(__FILE__),"<br>"; # C:\xampp\htdocs\mvc
echo __DIR__, "<br>"; 
echo dirname(__DIR__) , "<br>"; # C:\xampp\htdocs

# __DIR__ 과 DOCUMENT_ROOT의 차이점 
echo __DIR__, "<br>";  # C:\xampp\htdocs\mvc
echo $_SERVER['DOCUMENT_ROOT']; // C:/xampp/htdocs/mvc

```

> 네임스페이스
```php
namespace app\core; 
echo __NAMESPACE__; # app\core
```


<br>

## 서버변수 

```php 
<?php 
    echo $_SERVER['HTTP_HOST'], "<br>"; // localhost 현재서버의 호스
    echo $_SERVER['DOCUMENT_ROOT'], "<br>"; // 현재 프로젝트의 루트경로 
    echo $_SERVER['PHP_SELF'], "<br>"; // 현재 프로젝트의 상대경로 (쿼리스트링 제외)
    echo $_SERVER['REQUEST_URI'], "<br>"; // 파라미터로 전달된 쿼리스트링을 포함한다
    echo $_SERVER['REQUEST_METHOD'],"<br>"; // GET 또는 POST 반환

    ## http://localhost/app/view/main/main.php 요청했다면
    ## /app/view/main/main.php 을 출력한다. 
?>
```

<br>

```php
## 현재경로 http://localhost/application/view/main/index.php라 가정

$url = str_replace("index.php","","http://{$_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}");
# param1 : 이 문자열을 변경함 
# param2 : 이 문자열로 변경함 
# param3 : 대상 문자열 
# 대상 문자열에서 param1문자열을 param2문자열로 변경함 

echo $url;  
// 결과 : http://localhost/application/view/main/
```

<br>

## 함수 타입 정의 
```php
require_once 'test.lib.php';

## 다음과 같이 반환 타입을 지정할 수 있다. 
function test() : array {
    return ['easy','light'];
}
dumping(test());
```

<br>


## 유용한 함수 

<br>

> empty() 빈 문자열이면 true 반환 
```php
# 공백문자는 빈 문자열로 간주하지 않는다. 
$test = '  ';
$message = empty($test) ? "빈문자열" : "문자열있음"; 
echo $message; // 문자열있음
```
```php
# 그러므로 반드시 trim()함수로 공백문자를 제거하여 판한다. 
$test = '  ';
$test = trim($test); // 공백문자를 제거하였다. 
$message = empty($test) ? "빈문자열" : "문자열있음"; 
echo $message; // 빈문자열
```$test = '  ';
$test = trim($test);
$message = empty($test) ? "빈문자열" : "문자열있음"; 
echo $message; // 빈문자열
```

<br>

> explode() : 문자열을 자른 후 배열로 만든다. 
```php
$url = "member/join/212";
$urlArr =  explode("/",$url);
    # param 1 : 구분자
    # param 2 : 대상문자열
    # 구분자를 기준으로 대상문자열을 자른 후 배열로 만든다

var_dump($urlArr); // [ member, join, 212 ]
```

<br>

> isset() : 변수 값이 null아 아니면 true를 반환한다. 
- null이 아닐때 즉 값이 할당되었을 때 true를 반환한다. 
- 빈문자열과 공백문자열도 값이 할당 된것으로 본다. 
- 오직 선언만한 경우와 null값을 초기화한 경우 false를 반환한다. 
```php
$msg1 = '선언한 한 경우';
$test1; // 선언만

$msg2 = 'null값 초기화';
$test2 = null ; // null 값

$msg3 = '빈 문자열';
$test3 = ''; //빈문자열

$msg4 = "공백문자열";
$test4 = ' '; // 공백문자 

$msg5 = "값을 할당";
$test5 = 'isset test';

function issetTest($msg, $test){
    echo $msg;
    if(!isset($test)){
        echo " : 값이 설정되지 않았습니다.<br>";
        return;
    } 
    echo " : 값이 설정되었습니다.<br>";
}

issetTest($msg1 , $test1);
issetTest($msg2 , $test2);
issetTest($msg3 , $test3);
issetTest($msg4 , $test4);
issetTest($msg5 , $test5);
```
- $_GET['param'], $_POST['param'], $_REQUEST['param'] 등의 변수를 사용할 때 유용하다.
- 쿼리스트링이 전달되지 않았을때 이 변수에는 값이 할당되지 않아 이 때 PHP경고메세지가 출력된다.
- 다음과 같이하면 이런 문제를 해결 할 수 있다.
```php
$test = isset($_REQUEST['param']) ? $_REQUEST['param'] : '';
echo $test; 

```

<br>

> strpos()
```php
# 해당 문자의 index 번호를 반환한다. 
# 해당 문자를 찾지 못하면 false를 반환한다.  
$path = '01234?67'; 
$positon = strpos($path, '?'); // 5
echo $positon;
```

<br>

> substr()
```php
# @을 기준으로하여 문자열 자르기
    # @의 위치는 6
    $test = '012345@789';
    $result = substr($test, 0 , 6);
        // param1 : 대상문자열
        // param2 : 시작 (포함)
        // param3 : 끝 (포함안됨)
    echo $result, "<br>"; 
```

<br>

```php
$path = 'wwww.naver.com?id=easy'; 
$positon = strpos($path, '?'); 
echo substr($path, 0, $positon); // wwww.naver.com
```

<br>

> scandir()
- 대상 경로 디렉터리에 있는 모든 파일(디렉터리포함)들을 배열에 담아 반환한다. 
- 현재 경로 디렉터리는는 test이며 하위 디렉터리 및 파일은 다음과 같다. 
![scandir](/img/scandir.png)
```php
require_once 'test.lib.php';
dumping(scandir(__DIR__));
```
- 사용결과는 다음과 같다. 

![scandir_result](/img/scandir_result.png)

<br>

> array_diff()
```php
require_once 'test.lib.php';

$array1 = array("a" => "green", "red", "blue", "red");
$array2 = array("red","auqa");
$array3 = array("blue", "pink");
$result = array_diff($array1, $array2,$array3);

dumping($result);
```
- 수학 연산에서 차집합의 개념과 같다. 
- 위 예제에서는 $array1 - $array2 - $array3 
- 첫 번째 인수는 대상배열이며 이 배열에서 두 번째 이하 배열의 요소를 차집합 연산 수행을한다. 
- 두 번째 인수부터는 가변인자이다. 

<br>


## autoload 사용 
- 다음과 같은 디렉터리 구조를 만든다. 
- test 
    + app
        - App.php
    + controller
        - BoardController.php
        - MemberController.php
    + model
        - ModelBoard.php
        - ModelMember.php 
    + test.php

<br>

- 모든 클래스는 다음과 같이 정의한다.
- BoardController,MemberController,ModelBoard,ModelMember 다음과 같이 생성자만을 정의한다.
```php
class App {
    function __construct(){
        echo __CLASS__."객체생성<br>";
    }
}
```

```php
# 경로 변수 
define("_APP", __DIR__."/app/"); 
define("_MODEL", __DIR__."/model/"); 
define("_CONTROLLER", __DIR__."/controller/"); 

# 자동으로 실행될 함수 
function autoloadClass($className){
    
    // model 또는 app으로 시작하면 model 또는 app으로 문자열을 변경한다. 
    // 대상 문자열이 modelBoard 이면 model로 변경된다. 
    $name_test = preg_replace('/(model|app)(.*)/',"$1",strtolower($className));
    
    switch ($name_test) {
        case "app" : $dir = _APP;  
            break; 
        case "model" : $dir = _MODEL; 
            break; 
        default : $dir = _CONTROLLER; 
    }
    require_once "{$dir}{$className}.php"; 
}

# 자동으로 실행될 함수로 등록 
spl_autoload_register('autoloadClass');

# autoloadClass함수가 실행되면 해당 경로에 있는 클래스파일을 자동으로 불러온다. 
new BoardController; 
new MemberController;
new App; 
new ModelBoard;
new ModelMember;
```

<br>

## url을 쿼리스트링으로 변경
> /.htaccess
```
RewriteEngine On
RewriteRule ^([^.]*)/?$ index.php?param=$1 [L]
```
- localhost/test1/test2/test4 주소로 요청하면
- localhost/index.php?param=test1/test2/test4로 변환된
<br>

- 다른방법으로 httpd.conf파일에서 설정할 수 있다. 
>httpd.conf 파일 설정
```
DocumentRoot "C:/xampp/htdocs/mvc/public
<Directory "C:/xampp/htdocs/mvc/public">
    Options Indexes FollowSymLinks Includes ExecCGI
    AllowOverride All    
    Require all granted
    RewriteEngine On
    RewriteRule ^([^.]*)/?$ index.php?param=$1 [L]
</Directory>
```
<br>

## call_user_function

- 첫 번째 매개변수에서 제공한 콜백을 호출하고 
- 나머지 매개변수를 인수로 전달한다. 
```php
function foo($param1)
{
    echo "매개변수 출력 $param1 <br>";
}
call_user_func('foo', "사과");
```

<br>

> 인수로 전달하는 매개변수는 가변인자이다. 
```php
function foo($param1, $param2, $param3 )
{
    echo "$param1, $param2, $param3 <br>"; 
}
call_user_func("foo", '사과','딸기', '키위'); 
```

<br>

> 네임스페이스를 사용하는 경우 
```php
namespace app\test; 
require_once './test.lib.php';

function foo() {
    echo "네임스페이스를 사용하는 경우 콜백 호출";
}
echo __NAMESPACE__.'<br>'; # app\test 
call_user_func(__NAMESPACE__.'\foo'); # '\'반드시 붙여야한다.

```

<br>

> 클래스의 정적 메서드 호출
```php
require_once './test.lib.php';

class Test
{
    public static function foo(){
        echo "정적 메서드 호출"; 
    }
}
# 배열 전달 
call_user_func([Test::class, 'foo']);
```
- 다음과 같은 방법도 가능하다. 
```php
call_user_func(array(Test::class, 'foo'));
```
```php
call_user_func(Test::class.'::foo');
```

<br>

> 인스턴스 메서드 호출
```php
require_once './test.lib.php';
class Test
{
    public function foo(){
        echo "인스턴스 메서드 호출"; 
    }
}
$test = new Test(); // 객체생성 후 
call_user_func([$test, 'foo']); // 콜백 호출 
```

<br>

> 람다식 이용 
```php
call_user_func(function($param){
    echo "람다식 이용 : $param"; 
}, '인수 전달'); 
```

<br>

## ob_start(), ob_get_clean()
> 출력버퍼제어를 통해서 레이아웃 만들기 
layout.php
```html
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<nav>
    <h1>네비게이션</h1>
</nav>
{{contents}}
<footer>
    <h1>푸터</h1>
</footer>
</body>
</html>
```

content.php
```html
<h1>Hello!</h1>
```

test.php
```php
function layout(){
    ob_start(); 
    include_once 'layout.php'; 
    return ob_get_clean();
}

function contents(){
    ob_start(); 
    include_once 'content.php'; 
    return ob_get_clean();
}

function render(){
    $layout = layout();    
    $contents = contents(); 
    return str_replace('{{contents}}', $contents, $layout );
}

echo render(); 
```

<br>

## property_exists()
- 클래스에 인수로 전달한 멤버변수가 있으면 true를 반환한다. 
- property_exists('클래스 이름','멤버변수 이름')
```php
require_once 'test.lib.php';
class Person {
    public $name;
    public $age; 
    public $gender; 
}
dumping(property_exists('Person','name'));
```
- 인스턴스 객체에도 마찬가지로 적용된다. 
```php
require_once 'test.lib.php';
class Person {
    public $name;
    public $age; 
    public $gender; 
}
$person = new Person();
dumping(property_exists($person,'name'));
```
- 다음과같이 활용
```php
require_once 'test.lib.php';
class Person {
    public $name;
    public $age; 
    public $gender; 

    public function setData(array $data){
        foreach ($data as $key => $value) :
            if(property_exists($this, $key)):
                $this->$key = $value; 
            endif; 
        endforeach; 
    }
}
# 데이터를 배열로 전달하여 객체값 입력
$testData = [
    'name' => "leekwanghyup",
    'age' => 18,
    'gender' => "male",
    'easy' => 'easy'
]; 

$person = new Person(); 
$person->setData($testData);

dumping($person);
```
