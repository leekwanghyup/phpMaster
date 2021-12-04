# SECTION28 문자열 조작

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

## 문자열 바이트 수 얻기 
> strlen() mb_strlen()
```php
require 'lib.php';
$str = "안녕하세요.";
$test1 = strlen($str); // 16 문자열의 길이, 한글은 3
$test2 = mb_strlen($str); // 6 인코딩된 문자열의 길이, 한글도 1
multi_echo($test1, $test2);
```

<br>

## HTML 태그 무효처리
> htmlspecialchars()
```php
require 'lib.php';
$test = "<div class='name'>Test</div>";
echo htmlspecialchars($test);
```

<br>

## 문자열에서 HTML 태그 제거
> strip_tags()
```php
require 'lib.php';
$string = '<a href="http://www.naver.com">NAVER</a>';
$test = strip_tags($string);
multi_echo($test);
```

## 개행 코드 앞에 HTML 줄바꿈 태그 붙이기
> nl2br()
```php
require 'lib.php';
$test = "Lorem ipsum dolor sit, amet consectetur adipisicing elit. 
Ipsum deleniti corporis quidem animi ipsam
autem neque quas obcaecati eveniet velit, 
atque eum, sit error, tempora nobis adipisci dolores quis delectus?"; 
echo nl2br($test);
/*개행이 적용된 곳에 br 태그가 추가된다.*/
```

<br>

## 배열에서 문자열로 변환
> implode()
```php
require 'lib.php';
$arr = ['사과','배','귤','키위']; 
$arrToStr = implode(',', $arr); // param1 : 구분자
echo $arrToStr; 
# 사과,배,귤,키위
```

<br>

## 오류가 되는 문자열에 이스케이프 추가 
> addslashes()
```php
require 'lib.php';
$arr = ['"사과"','"배"','"귤"','"키위"']; 
$arrToStr = implode(',',$arr); 
dumping($arrToStr);             #  ""사과","배","귤","키위""
dumping(addslashes($arrToStr)); # "\"사과\",\"배\",\"귤\",\"키위\""
```
<br>

## 문자열에서 배열로
```php
require 'lib.php';
$str = "사과,배,귤,키위"; 
$strToArr = explode(',' , $str); 
dumping($strToArr);
/*
[0]=>
  string(6) "사과"
  [1]=>
  string(3) "배"
  [2]=>
  string(3) "귤"
  [3]=>
  string(6) "키위"
*/
```

<br>

# SECTION29 배열조작

## 오름차순 정렬
> sort()
```php
require_once 'lib.php';
$arr = [12,2,4,13,7]; 
sort($arr); 
dumping($arr)
```

<br>

## 내림차순 정렬
>rsort()
```php
require_once 'lib.php';
$arr = [12,2,4,13,7]; 
rsort($arr); 
dumping($arr)
```

<br>

## 문자열의 정렬
> sort() rsort()
```php
require_once 'lib.php';
$arr = ['b','A','c','d','B','D','C','a'];

sort($arr); 
echo "<h3>사전순 정렬</h3>";
dumping($arr);

rsort($arr); 
echo "<h3>사전역순 정렬</h3>";
dumping($arr);
```

<br>

> 플래그 지정  
```php
require_once 'lib.php';
$arr = ['11','2','1','7','21'];
sort($arr); // 숫자로 인식하여 정렬함 
dumping($arr);
/*
array(5) {
  [0]=>
  string(1) "1"
  [1]=>
  string(1) "2"
  [2]=>
  string(1) "7"
  [3]=>
  string(2) "11"
  [4]=>
  string(2) "21"
}
*/

sort($arr,SORT_STRING); // 문자로 인식하여 정렬
dumping($arr);
/*
array(5) {
  [0]=>
  string(1) "1"
  [1]=>
  string(2) "11"
  [2]=>
  string(1) "2"
  [3]=>
  string(2) "21"
  [4]=>
  string(1) "7"
}
*/
```
<br>

## 연관배열의 정렬
> asort() ksort()
```PHP
require_once 'lib.php';
$arr = [
    "MIKE"=>"1200",
    "TV"=>"10000",
    "RADIO"=>"5000",
    "VIDEO"=>"7000",
    "AUDIO"=>"6000",
]; 

# 값을 기준으로 데이터 정렬 
asort($arr);

# arsort() 함수를 사용하면 내림차순으로 정렬된다.
dumping($arr);
/*
array(5) {
  ["MIKE"]=>
  string(4) "1200"
  ["RADIO"]=>
  string(4) "5000"
  ["AUDIO"]=>
  string(4) "6000"
  ["VIDEO"]=>
  string(4) "7000"
  ["TV"]=>
  string(5) "10000"
}
*/

# 키를 기준으로 데이터 정렬
ksort($arr); 

# krsort()함수를 사용하면 내림차순으로 정렬된다. 
dumping($arr);
/*
array(5) {
  ["AUDIO"]=>
  string(4) "6000"
  ["MIKE"]=>
  string(4) "1200"
  ["RADIO"]=>
  string(4) "5000"
  ["TV"]=>
  string(5) "10000"
  ["VIDEO"]=>
  string(4) "7000"
}
*/
```

<br>

## 배열 끝에 데이터 추가/삭제 
> array_pop() array_push()
```php
require_once 'lib.php';
$arr = ['사과','키위','귤']; 

# 배열 끝에 데이터 삭제  
$poped =  array_pop($arr);

# 꺼낸 데이터를 변수에 할당하여 저장할 수 있다. 
dumping($poped); 
/*
    string(3) "귤"
*/

dumping($arr); 
/*
array(2) {
  [0]=>
  string(6) "사과"
  [1]=>
  string(6) "키위"
}
*/

# 배열끝에 데이터 추가  
array_push($arr, '수박'); 
    # param1 : 대상배열 지정
    # param2 : 배열요소 
    # param1 배열에 param2를 배열요소로 추가한다. 
dumping($arr); 
/*
array(3) {
  [0]=>
  string(6) "사과"
  [1]=>
  string(6) "키위"
  [2]=>
  string(6) "수박"
}
*/
```

<br>

## 배열 앞 데이터 추가/삭제 
> array_unshift(), array_shift()
```php
require_once 'lib.php';
$arr = ['사과','키위','귤']; 

# 배열 앞에 데이터 추가 
# array_push()와 같은 방법으로 사용한다. 
array_unshift($arr, '수박'); 
dumping($arr);
/*
array(4) {
  [0]=>
  string(6) "수박"
  [1]=>
  string(6) "사과"
  [2]=>
  string(6) "키위"
  [3]=>
  string(3) "귤"
}
*/

$shifted = array_shift($arr); // 수박
dumping($arr);
```

<br>

## 배열 병함
> array_merge()
```php
require_once 'lib.php';
$arr1 = ['1','2','3','4'];
$arr2 = ['사과','배'];
$arr3 = ['a','b'];

# 배열 병합
$result = array_merge($arr1, $arr2,$arr3);
    # param : 배열, 두 번째 인수부터 가변인수다.  
    # 배열들을 병합하여 새로운 배열을 생성한다. 
    # 원본배열은 변하지 않는다. 

dumping($result);
/*
array(8) {
  [0]=>
  string(1) "1"
  [1]=>
  string(1) "2"
  [2]=>
  string(1) "3"
  [3]=>
  string(1) "4"
  [4]=>
  string(6) "사과"
  [5]=>
  string(3) "배"
  [6]=>
  string(1) "a"
  [7]=>
  string(1) "b"
}
*/
```

<br>

## 배열 자르기
> array_slice()
```php
require_once 'lib.php';
$arr = ['0','1','2','3','4','5','6'];

$result = array_slice($arr, 2, 3); 
    # param1 : 대상 배열
    # param2 : 슬라이스의 시작위치 
    # parma3 : 길이 
    # param1의 배열에서 param2의 인덱스위치로부터 param3길이만큼 자른다.
    # 원본배열은 변하지 않는다. 

dumping($result);
/*
array(3) {
  [0]=>
  string(1) "2"
  [1]=>
  string(1) "3"
  [2]=>
  string(1) "4"
}
*/
```

<br>

## 배열데이터 데이터 반전
> array_reverse()
```php
require_once 'lib.php';
$arr = ['다마네기','당근','시금치'];

$result = array_reverse($arr);
    # 원본배열은 변하지 않는다. 

dumping($result);
/*
array(3) {
  [0]=>
  string(9) "시금치"
  [1]=>
  string(6) "당근"
  [2]=>
  string(12) "다마네기"
}
*/
```
