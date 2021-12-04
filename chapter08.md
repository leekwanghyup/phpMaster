#  SECTION65 데이터베이스로 접속하려면

## mysql함수를 사용한 접속 
```php
$localhost = 'localhost'; 
    $user = 'root'; 
    $password = '1234'; 

    // Mysql 연결객체 
    $con = mysqli_connect($localhost, $user, $password) or die('접속실패');
    echo "MySQL 접속 성공"; 

    mysqli_close($con); // 연결종료 
```

<br>

## mysqli클래스를 사용한 접속
```php
$localhost = 'localhost'; 
    $user = 'root'; 
    $password = '1234'; 
    $dbName = 'shopdb';

    $mysqli = new mysqli($localhost, $user, $password,$dbName); 
    if($mysqli->connect_error){
        die("접속 실패 : ".$mysqli->connect_errno." >>>> ".$mysqli->connect_error); 
    }
    echo "접속 성공"; 
    $mysqli->close(); 
```

<br>

# SECTION66 PDO를 이용하려면 

## PDO를 이용한 접속
```php
$localhost = 'localhost'; 
$user = 'root'; 
$password = '1234'; 
$dbName = 'shopdb';
$dsn = "mysql::host=$localhost;dbname=$dbName;charset=utf8"; 

try {
    $pdo = new PDO($dsn, $user, $password); // 접속
    $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION); 
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); 
    echo "접속성공"; 
} catch(PDOException $exception){
    die('접속오류 : '.$exception->getMessage()); 
}
```

<br>

## mysql 연결 객체 분리 
- mysqlCon.php 
```php 
<?php 
function mysql_con(){

    $localhost = 'localhost'; 
    $user = 'root'; 
    $password = '1234'; 
    $dbName = 'shopdb';
    $dsn = "mysql::host=$localhost;dbname=$dbName;charset=utf8"; 

    try {
        $pdo = new PDO($dsn, $user, $password); // 접속
        $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION); 
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); 
        return $pdo; 
    } catch(PDOException $exception){
        die('접속오류 : '.$exception->getMessage()); 
    }
}    
?>
```

<br>

# Section67 SQL문을 발행하려면 

## 값을 결합하기 1
- insertForm.php
``` php 
<form action="insertProc.php" method="post">
    아이디 : <input type="text" name="memberId"> <br>
    이름 : <input type="text" name="memberName"> <br>
    주소 : <input type="text" name="memberAddress"> <br>
    <button type="submit">가입</button>
</form>
```
- insertProc.php 
```php 
$memberId = $_REQUEST['memberId'];
$memberName = $_REQUEST['memberName'];
$memberAddress = $_REQUEST['memberAddress'];

try {
    require_once('mysqlCon.php');
    $pdo = mysql_con(); 

    $pdo->beginTransaction();
    $sql = "insert into membertbl values(:mid,:mName, :mAddr)"; // 1. 쿼리문
    $stmt = $pdo->prepare($sql);  // 2. STMT 객체에 쿼리문 전달 
    $stmt->bindValue(':mid', $memberId);  // 3. 데이터 바인딩
    $stmt->bindValue(':mName', $memberName); 
    $stmt->bindValue(':mAddr', $memberAddress); 
    $stmt->execute(); // 4. 실행 
    $pdo->commit();

} catch(PDOException $excetpion){
    $pdo->rollback();
    die('접속오류 : '.$exception->getMessage()); 
}
```

## 값을 결합하기 2

<br>

```php
$memberId = $_REQUEST['memberId'];
$memberName = $_REQUEST['memberName'];
$memberAddress = $_REQUEST['memberAddress'];

try {
    require_once('mysqlCon.php');
    $pdo = mysql_con(); 

    $pdo->beginTransaction();
    $sql = "insert into membertbl values(?,?,?)"; 
    $stmt = $pdo->prepare($sql); 
    $stmt->bindValue(1, $memberId); 
    $stmt->bindValue(2, $memberName); 
    $stmt->bindValue(3, $memberAddress); 
    $stmt->execute(); 
    $pdo->commit();

} catch(PDOException $excetpion){
    $pdo->rollback();
    die('접속오류 : '.$exception->getMessage()); 
}
```

<br>

>데이터 타입 설정
```php
$stmt->bindValue(1, $memberId,PDO::PARAM_STR); 
$stmt->bindValue(2, $memberName, PDO::PARAM_STR); 
$stmt->bindValue(3, $memberAddress,PDO::PARAM_STR); 
```

## 값 결합하기3 
```php
$memberId = $_REQUEST['memberId'];
$memberName = $_REQUEST['memberName'];
$memberAddress = $_REQUEST['memberAddress'];

try {
    require_once('mysqlCon.php');
    $pdo = mysql_con(); 
    
    $pdo->beginTransaction();
    $sql = "insert into membertbl values(?,?,?)"; 
    $stmt = $pdo->prepare($sql); 
    $stmt->execute(array($memberId,$memberName,$memberAddress)); 
    $pdo->commit();

} catch(PDOException $excetpion){
    $pdo->rollback();
    die('접속오류 : '.$exception->getMessage()); 
}
```


# SECTION 69 데이터 검색 

## 스키마 및 테이블 생성 
```sql
create schema phpMaster;
use phpMaster;
create table member(
	id int unsigned not null auto_increment primary key,
    last_name varchar(50),
    first_name varchar(50),
    age tinyint unsigned
);
```

## 데이터 추가
```sql
INSERT INTO member VALUES (null,'예가', '앨런', '17');
INSERT INTO member VALUES (null,'아커먼', '미카사', '17');
INSERT INTO member VALUES (null,'아커먼', '리바이', '30');
INSERT INTO member VALUES (null,'예가', '지크', '25');
INSERT INTO member VALUES (null,'스미스', '앨빈', '30');
INSERT INTO member VALUES (null,'조에', '한지', '28');
INSERT INTO member VALUES (null,'레이스', '히스토리아', '16');
```

<br>

## LIKE문 예제
```sql
select * from member where first_name like '%토리%';
select * from member where last_name like '예%';

select * from member 
where first_name like '%토리%'
or last_name like '예%';

select * from member 
where first_name like '앨%'
and last_name like '예%';
```

<br>

## 검색폼
```php 
<form action="searchProc.php">
    이름 : <input type="text" name="search_key">
    <button>검색</button>
</form>
```

<br>

## 검색처리
```php
<?php 
$seach_key = "%".$_REQUEST['search_key']."%"; 

try{
    require_once('mysqlCon.php');
    # DBName을 변경한다(phpMaster)
    $pdo = mysql_con();

    $sql = "select * from member where last_name like ? or first_name like ?";
    $stmt = $pdo->prepare($sql); 
    $stmt->bindValue(1,$seach_key,PDO::PARAM_STR);
    $stmt->bindValue(2,$seach_key,PDO::PARAM_STR);
    $stmt->execute(); 

    // 검색된 결과 
    $count = $stmt->rowCount(); 
    echo "검색결과 : $count 건"; 

} catch(PDOException $exception){
    die('접속오류 : '.$exception->getMessage()); 
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php while($rowResult = $stmt->fetch(PDO::FETCH_ASSOC)){ ?>
    <ul>
        <li>성 : <?=  htmlspecialchars($rowResult['first_name'])  ?> </li>
        <li>이름 : <?= htmlspecialchars($rowResult['last_name'])  ?> </li>
        <li>나이 : <?= htmlspecialchars($rowResult['age']) ?> </li>
    </ul>
    <?php } ?>
</body>
</html>
```
<br>

# SECTION70 데이터 수정
updateForm.php
```php
<?php
    session_start();
    require_once('mysqlCon.php');

    $pdo = mysql_con(); 

    $id = 1; 
    $_SESSION['id'] = $id; 

    try{
        $sql = "select * from member where id = ?"; 
        $stmt = $pdo->prepare($sql); 
        $stmt -> bindValue(1,$id,PDO::PARAM_INT); 
        $stmt-> execute();
        $count = $stmt->rowCount();
        echo "$count"; 
        $row = $stmt->fetch(PDO::FETCH_ASSOC); 

    } catch(PDOException $exception){
        die('접속실패'.$exception->getMessage()); 
    }
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>업데이트폼</title>
</head>
<body>
    <form action="updateProc.php" method="POST">
        번호 :  <?= $row['id'] ?> <br>
        성 :  <input type="text" name="last_name" value="<?= $row['last_name'] ?>"><br>
        이름 : <input type="text" name="first_name" value="<?= $row['first_name']  ?>"><br>
        연령 : <input type="text" name="age" value="<?= $row['age']  ?>"><br>
        <button>수정</button>
    </form>
</body>
</html>
```

<br>

updateProc.php
```php
<?php 
    session_start(); // 세션시작
    $id = $_SESSION['id'];
    
    $first_name = $_REQUEST['first_name']; 
    $last_name = $_REQUEST['last_name'];
    $age = $_REQUEST['age']; 

    require_once('mysqlCon.php');
    $pdo = mysql_con(); 

    try{
        $pdo->beginTransaction();
        $sql = "update member set last_name = ?, first_name = ?, age = ? where id = ?"; 
        $stmt = $pdo->prepare($sql); 
        $stmt -> bindValue(1,$last_name,PDO::PARAM_STR); 
        $stmt -> bindValue(2,$first_name,PDO::PARAM_STR); 
        $stmt -> bindValue(3,$age,PDO::PARAM_STR); 
        $stmt -> bindValue(4,$id,PDO::PARAM_INT); 
        $stmt-> execute();
        $pdo->commit();

        echo "데이터를 수정함";

    } catch(PDOException $exception){
        die('접속실패'.$exception->getMessage()); 
        $pdo->rollBack();
    }

    $_SESSION = array(); // 세션변수 삭제 
    session_destroy();  // 세션 종료   
?>
```

<br>

# SECTION71 데이터 삭제
deleteForm.php
```php
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<p>삭제할 번호를 선택하세요</p>
<ul>
    <?php for($i=1; $i<8; $i++) {?>
    <li>
        <a href="deleteProc.php?action=delete&id=<?= $i ?>">[<?= $i ?>]</a>
    </li>
    <?php }?>
</ul>
</body>
</html>
<style>
    li { padding: 10px;}
    a { font-size: 20px;}
</style>
```

<br>

deleteProc.php
```php
<?php
    $id = $_REQUEST['id'] ;
    $action = $_REQUEST['action'];  

    require_once('mysqlCon.php');
    $pdo = mysql_con(); 

    if(isset($action) && ($action=='delete') && ($id>0) ){

        try {
            $pdo->beginTransaction();
            $sql = "delete from member where id = ?"; 
            $stmt = $pdo->prepare($sql); 
            $stmt->bindValue(1, $id,PDO::PARAM_INT); 
            $stmt->execute(); 
            $pdo->commit();
            
            echo "데이터를 삭제함"; 
        } catch(PDOException $excetpion){
            
            die('오류 : '.$exception->getMessage()); 
        }

    } else {
        echo "잘못된 접근";
    }
?>
```

# 데이터 리스트 
```php 
<?php 
require_once('mysqlCon.php');
$pdo = mysql_con();

try{
    $sql = "select * from member";

    $stmt = $pdo->query($sql); 
    $rowResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $count = $stmt->rowCount(); 

} catch(PDOException $exception){
    die('접속오류 : '.$exception->getMessage()); 
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php foreach( $rowResult as $member ) { ?>
    <ul>
        <li>번호 : <?=  htmlspecialchars($member['id'])  ?> </li>
        <li>성 : <?=  htmlspecialchars($member['first_name'])  ?> </li>
        <li>이름 : <?= htmlspecialchars($member['last_name'])  ?> </li>
        <li>나이 : <?= htmlspecialchars($member['age']) ?> </li>
    </ul>
    <?php } ?>
</body>
</html>
```


