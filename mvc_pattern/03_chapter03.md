

## 데이터베이스 연결 

- Mysql에 연결하기 위해 PDO객체를 사용할 것이다.
- $dsn, $user, $password 변수는 아직 정의 되지 않았다. 
- .env파일을 사용하여 외부에서 이 값을 가져올 것이다.
> Database.php
```php 
namespace app\core; 

class Database
{
    public PDO $pdo; 

    public function __construct(array $config)
    {
        $this->pdo = new PDO($dsn, $user, $password); // 아직 정의 되지 않음
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
    }
}
```

- Application 객체에서 Database객체를 생성한다. 
> Application.php
```php
public Database $db; 

public function __construct(string $ROOT_DIR)
{
    /* ... */
    $this->db = new Database();
}

```

- 루트폴더에 .env파일을 만든다.
- 이 파일은 데이터베이스 접속정보를 가지고있다.
- mvc_framework 데이터베이스가 만들어져 있어야 한다. 
- index.php에서 이 파일의 내용을 읽어 Application 객체에 주입할 예정이다.
> .env
```
DB_DSN = mysql:host=localhost;port=3306;dbname=mvc_framework
DB_USER = root
DB_PASSWORD = 1234
```

<br>

- https://github.com/vlucas/phpdotenv
- git bahs에서 다음을 실행 
```
composer require vlucas/phpdotenv
```

<br>

- Dotenv에 정의된 createMutable() 정적메서드는 .env파일을 읽을 수 있도록 해준다. 
```php
$dotenv = Dotenv\Dotenv::createMutable(__DIR__);
$dotenv->load();
```

<br>

> index.php
```PHP
$dotenv = Dotenv\Dotenv::createMutable(dirname(__DIR__)); // 현재 경로가 public이므로 루트 경로로 만들어준다.
$dotenv->load();

$config = [
    'db' => [
        'dsn' => $_ENV['DB_DSN'], 
        'user' => $_ENV['DB_USER'], 
        'password' => $_ENV['DB_PASSWORD'], 
    ]
]; 

$app = new Application(dirname(__DIR__), $config); 
```

> Database.php
```php
public function __construct(array $config)
{
    $dsn = $config['dsn'] ?? '';
    $user = $config['user'] ?? '';
    $password = $config['password'] ?? '';   
    $this->pdo = new PDO($dsn, $user, $password);
    $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

}
```

<br>

> Application
```php
public function __construct(string $ROOT_DIR, array $config)
{
 /* ...  */
    $this->db = new Database($config['db']);
}
```

<br>


- 브라우저에서 localhost로 요청해보자.
- 아무런 오류 메세지가 없다면 데이터베이스에 연결된 것 이다.


## 마이그레이션
- migrations 폴더는 루트 경로아래 생성한다.
- migrations 폴더 아래 m0001_initial.php 파일 생성 후 클래스 정의
- migrations 폴더 아래 m0002_something.php 파일 생성 후 클래스 정의
- 프로젝트루트 폴더 아래 migrations.php 파일 생성
- core 폴더 아래 Database.php 파일 생성 및 클래스 정의 
```php
class m0001_initial
{
    public function up()
    {
        echo "Application migration m0001_initial".PHP_EOL; 
    }

    public function down()
    {
        echo "Down migration m0001_initial".PHP_EOL;
    }
}
```
```php
class m0002_something
{
    public function up()
    {
        echo "Application migration m0002_something".PHP_EOL; 
    }

    public function down()
    {
        echo "Down migration m0002_something".PHP_EOL;
    } 
}
```

> Database 
```php
public function applyMigrations()
{
    $this->createMigrationTable(); 
    $this->getAppliedMigrations(); 
    $files = scandir(Application::$ROOT_DIR.'/migrations'); 
}

public function createMigrationTable()
{
    # 테이블 생성
    $this->pdo->exec("CREATE TABLE IF NOT EXISTS migrations(
            id INT auto_increment PRIMARY KEY, 
            migration VARCHAR(255), 
            create_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)"); 
}

public function getAppliedMigrations()
{
    $statement = $this->pdo->prepare("SELECT migration FROM migrations"); 
    $statement->execute(); 
    return $statement->fetchAll(PDO::FETCH_COLUMN);         
}
```

migrations.php
```php

<?php 
use app\core\Application;
include __DIR__.'/lib/test.lib.php';
include __DIR__.'/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createMutable(__DIR__);
$dotenv->load();

$config = [
    'db' => [
        'dsn' => $_ENV['DB_DSN'], 
        'user' => $_ENV['DB_USER'], 
        'password' => $_ENV['DB_PASSWORD'], 
    ]
]; 

$app = new Application(__DIR__, $config); 

$app->db->applyMigrations(); 
```

- 프로젝트 루트폴더 명령줄(또는 gitbash)에서 다음을 실행한다.
```
php migrations.php
```

> Database
```php
public function applyMigrations()
{
    $this->createMigrationTable(); 
    $appliedMigrations =  $this->getAppliedMigrations();  // 적용된 migration
    $files = scandir(Application::$ROOT_DIR.'/migrations');  // 모든 migration
    $toApplyMigrations = array_diff($files, $appliedMigrations); // 아직 적용되지 않은 migration
    foreach ($toApplyMigrations as $migration){
        if($migration === '.' || $migration === '..'){
            continue; 
        }
        require_once Application::$ROOT_DIR.'/migrations/'.$migration;
        
        $className = pathinfo($migration, PATHINFO_FILENAME); // 파일이름(확장자 제외)
        $instance = new $className(); 
        echo "Applying migration $migration".PHP_EOL; 
        $instance->up(); 
        echo "Applyied migration $migration".PHP_EOL; 
    }
}
```
- 프로젝트 루트폴더 명령줄(또는 gitbash)에서 다음을 실행한다.
```
php migrations.php
```

```php
public function applyMigrations()
{
    $this->createMigrationTable(); 
    $appliedMigrations =  $this->getAppliedMigrations();  // 적용된 migration

    $newMigrations = []; ### 추가 
    /* ... */
    foreach ($toApplyMigrations as $migration){
        /* ... */
        echo "Applyied migration $migration".PHP_EOL; 
        
        $newMigrations[] = $migration; ### 추가 // 작업한 migration파일일을 배열에 저장한다.
    }
    ### 추가 
    if(!empty($newMigrations)){  // 새로작업한 migration파일이 있다면 
        $this->saveMigrations($newMigrations); // 데이터베이스에 migrations테이블에 기록한다.
    } else {
        echo "All migration are applied"; 
    }
}

public function saveMigrations(array $migrations)
{
    $migrations =  array_map(fn($m)=> "('$m')", $migrations);
    $str = implode(",", $migrations);
    $stmt = $this->pdo->prepare("INSERT INTO migrations (migration) VALUES $str"); 
    $stmt->execute(); 
}
```

- 로그메세지 적용 
```php
public function log(string $message)
{
    echo '[ '.date('Y-m-d H:i:s'). ' ] - '.$message.PHP_EOL; 
}

## echo가 적용된 모든 문자열을 log함수로 감싼다. 
/* ... */
        $this->log("Applying migration $migration");
        $instance->up(); 
        $this->log("Applyied migration $migration"); 
        $newMigrations[] = $migration; 
    }
    if(!empty($newMigrations)){
        $this->saveMigrations($newMigrations);
    } else {
        $this->log("All migration are applied");
    }
}
```

<br>

> m0001_initial.php
```php
use app\core\Application;

class m0001_initial
{
    public function up()
    {
        $db = Application::$app ->db; 
        $sql = "CREATE TABLE users(
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) NOT NULL, 
            firstname VARCHAR(255) NOT NULL,
            lastname VARCHAR(255) NOT NULL,
            status TINYINT NOT NULL DEFAULT 0,
            create_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
        )";
        $db->pdo->exec($sql);
    }

    public function down()
    {
        $db = Application::$app ->db; 
        $sql = "DROP TABLE users";
        $db->pdo->exec($sql);
    }
}
```
- m0002_something.php 파일을 삭제하고 다음을 실행한다.
```
php migrations.php
```

- 패스워드 컬럼을 추가하는 마이그레이션을 생성하자.
- m0002_add_password_column.php
```php
use app\core\Application;

class m0002_add_password_column
{
    public function up()
    {
        $db = Application::$app ->db; 
        $db->pdo->exec("ALTER TABLE users ADD COLUMN password VARCHAR(512) NOT NULL");
    }

    public function down()
    {
        $db = Application::$app ->db; 
        $db->pdo->exec("ALTER TABLE users DROP COLUMN password");
     
    }
}
```

