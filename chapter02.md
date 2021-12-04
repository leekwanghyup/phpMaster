# 배열

## SECTION 데이터 사용하기 

```php
$week[] = "월"; 
$week[] = "화"; 
$week[] = "수";
echo $week[0], "<br>"; 
echo $week[1], "<br>"; 
echo $week[2], "<br>"; 
```

```php
$week[0] = "월"; 
$week[1] = "화"; 
$week[2] = "수";
echo $week[0], "<br>"; 
echo $week[1], "<br>"; 
echo $week[2], "<br>"; 
```

```php
$week = array("월",'화','수'); 
echo $week[0], "<br>";
echo $week[1], "<br>";
echo $week[2], "<br>";

```

```php
$week = ["월",'화','수']; // php 5.4 이상 
echo $week[0], "<br>";
echo $week[1], "<br>";
echo $week[2], "<br>";
```

### 반복
```php
$week = ["월",'화','수']; 

# count($week) 배열의 길이 반환
for($i=0; $i<count($week) ; $i++){
    echo $week[$i] , "<br>"; 
}
```
```php
$week = ["월",'화','수']; 

foreach ($week as $value){
    echo $value , "<br>";
}
```

## SECTION15 연관배열
```php
$member['name'] = '김철수'; 
$member['age'] = 19;
$member['height'] = 171;

echo $member['name'] , "<br>";
echo $member['age'] , "<br>";
echo $member['height'] , "<br>";


```

```php
$member = array(
    'name'=>'이명박',
    'age'=> 70,
    'height'=> '162'
);

echo $member['name'] , "<br>";
echo $member['age'] , "<br>";
echo $member['height'] , "<br>";
```

```php
$member = [
    'name'=>'박근헤',
    'age'=> 70,
    'height'=> '158'
];

echo $member['name'] , "<br>";
echo $member['age'] , "<br>";
echo $member['height'] , "<br>";
```

### 반복
```php
$member = array(
    'name'=>'이명박',
    'age'=> 70,
    'height'=> '162'
);
foreach ($member as $value){
    echo $value, "<br>"; 
}

foreach ($member as $key => $value){
    echo $key." : ".$value, "<br>"; 
}
```

