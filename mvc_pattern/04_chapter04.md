## 데이터 인서트 

> DBModel
```php
use app\core\Application;
use app\models\Model;

abstract class DbModel extends Model 
{
    abstract public function tableName() : string; 

    abstract public function attribute() : array; //
    
    public function save()
    {
        $tableName = $this->tableName(); // 테이블명 
        $attribute = $this->attribute(); // 모델객체의 멤버변수명

        // ex) INSERT INTO user (firstname, lastname, email ) values (:firstname, :lastname, :email ) 
        $column =  implode(",",$attribute); // firstname, lastname, email 
        $values = array_map(fn($attr) => ":$attr",$attribute); // :firstname, :lastname, :email
        $stmt = self::prepare("INSERT INTO $tableName ( $column ) VALUES ( $values ) "); 

        dumping($stmt); // sql문이 잘 들어갔는지 확인한다. 아직 실행되지 않는다. 
    }

    private static function prepare($sql)
    {
        return Application::$app->db->pdo->prepare($sql);
    }
}

```

<br>

> RegisterModel
```php
use app\core\DbModel;

class RegisterModel extends DbModel
{
    /* ...  */
    public function tableName(): string
    {
        return "users";
    }

    public function attribute(): array
    {
        $className = get_class($this); // RegisterModel
        $attr_value = get_class_vars($className); 
        // array { ['firstname']=> '', ['lastname']=> '', ['email']=> '',['password']=> '',['confirmPassword']=> '' ['errors'] => ''}
        $attr = array_keys($attr_value); // ['firstname', 'lastname', 'email', 'password', 'confirmPassword', 'errors']
        return array_diff($attr, ['confirmPassword','errors']); // ['firstname', 'lastname', 'email', 'password']
    }
    /* ... */
    public function register()
    {
        return $this->save(); 
    }
}
```
<br>

- 여기까지 했다면 statement 객체에 sql문이 올바로 들어왔는지 확인한다.

<br>

> DbModel
```php
public function save()
{
    /* ... */
    $stmt = self::prepare("INSERT INTO $tableName ( $column ) VALUES ( $values ) "); 
    
    // 예를 들면  :firstname 에 Register 객체의 firstname 값이 바인딩된다. 
    foreach ($attribute as $attribute)
    {
        $stmt->bindValue(":$attribute", $this->{$attribute} ); 
    }
    $stmt->execute(); 
    return true; 
}
```

<br>

- user 테이블 lastname 컬럼 오타
- status 컬럼 기본값 설정 다시 함

<br>

## 비밀번호 암호화 

- ResiterModelController register() 메서드 이름을 save()로 변경한다. 
- 즉 부모클래스인 ModelDb save()메서드를 오버라이딩 한다. 
- authController에서도 이 메서드의 이름을 변경해야한다. 

<br>

> ResiterModelController
```php
public function save()
{
    $this->password = password_hash($this->password, PASSWORD_DEFAULT); // 암호화 
    return parent::save(); // 부모메서드 호출
}
```

<br>

> AuthController
```php
public function register(Request $request)
{
    /* ... */            
    if($registerModel->validate() && $registerModel->save()){
        return "Success";
    }
    /* ... */            
}
```

## status 컬럼 처리 

> RegisterModel
```php
const STATUS_INATIVE = 0; 
const STATUS_ACTIVE = 1; 
const STATUS_DELETED = 2; 

/* ... */
public int $status = self::STATUS_INATIVE; //

/* ... */
public function save()
{
    $this->status = self::STATUS_INATIVE; // 테스트 할 때에는 다른 값을 넣는다. 
    $this->password = password_hash($this->password, PASSWORD_DEFAULT); 
    return parent::save(); 
}
```

<br>

## 이메일 중복 제거 

> Model 
```php
public const RULE_UNIQUE = 'unique';

```

> RegisterModel
```php
public function rules() : array
{
    return [
                            /* ... */
        # RULE_UNIQUE 추가 
        'email' => [ self::RULE_REQUIRED, self::RULE_EMAIL, 
            [self::RULE_UNIQUE, 'class' => self::class, 'attribute'=> 'email'] 
        ],
                            /* ... */
    ];
}
```
<br>

> Model validate() 메서드의 유효성을 검사하는 조건문
```php
if($ruleName === self::RULE_UNIQUE){
    $className = $rule['class'];  // 클래스 이름
    $uniqueAttr = $rule['attribute'] ?? $attribute; // 유니크 속성을 가진 컬럼
    $tableName = $className::tableName();  // 적용테이블 이름 .. 현재 생성된 객체가 자식 객체이고 상속 관계로 이어진 경우 정적메소드 호출이 가능하다.
    $stmt = Application::$app->db->pdo->prepare("SELECT * FROM $tableName where $uniqueAttr = :attr ");
    $stmt->bindValue(":attr", $value); 
    $stmt->execute(); 
    $record = $stmt->fetchObject(); // 하나의 레코드만 결과로 가져온다. 
    if($record) { // 해당 레코드가 존재하는 경우 
        $this->addError($attribute, self::RULE_UNIQUE, $rule);
    }
}

/* ... */
public function errorMessage()
{
    return
    [
        /* ... 오류메세지 추가  */
        self::RULE_UNIQUE => 'Record with this {attribute} already exists', 
    ]; 
}
```
- 메일이 같은 사용자를 두 명 등록하여 테스트한다.

<br>

## 인서트 성공 후 리다이렉션 
> Response 
```php
public function redirect(string $url)
{
    header('Location: '.$url); 
}
```

<br>

- 회원가입이 성공한 경우 '회원가입을 축하합니다.'라는 메세지를 전달할 수 있다. 
- 이 메세지는 회원가입 후 볼 수 있는 페이지에 한하여야 하며 사용자가 다른 페이지로 이동할 경우 삭제되어야 한다. 
- 이것을 세션플래쉬 메세지라고 한다. 
- 세션플래쉬는 하나의 요청을 처리 후 곧 바로 소멸된다. 

<br>

> Session
```php
<?php 
namespace app\core;

class Session 
{
    public const FLASH_KEY = 'flash_message';

    public function __construct()
    {
        session_start(); 
        $flashMessages = $_SESSION[self::FLASH_KEY] ?? []; 
        
        foreach($flashMessages as $key => &$flashMessage)
        {
            $flashMessage['remove'] = true;
        }
        $_SESSION[self::FLASH_KEY] = $flashMessages; 
        
    }

    public function __destruct()
    {
        $flashMessages = $_SESSION[self::FLASH_KEY] ?? []; 
        
        foreach($flashMessages as $key => &$flashMessage)
        {
            if($flashMessage['remove']){
                unset($flashMessages[$key]);
            }
        }
        $_SESSION[self::FLASH_KEY] = $flashMessages; 
    }


    public function setFlash($key, $message)
    {
        $_SESSION[self::FLASH_KEY][$key] = [
            'remove' => false, 
            'value' => $message
        ];
    }

    public function getFlash($key)
    {
        return $_SESSION[self::FLASH_KEY][$key]['value'] ?? false; 
    }
}
```
<br>

> Application
```php

public Session $session; 
/* ... */
public function __construct(string $ROOT_DIR, array $config)
{
/* ... */
    $this->session = new Session(); 
/* ... */
}
```

<br>

> main.php
```php
<div>
    <?php if(Application::$app->session->getFlash('success')): ?>
        <div>
            <?php echo Application::$app->session->getFlash('success') ?>    
        </div>
    <?php endif ?>
    {{contents}}
</div>
```

<br>

> AuthController 
```php
public function register(Request $request)
{
    /* ... */    
        if($registerModel->validate() && $registerModel->save()){
            Application::$app->session->setFlash('success', 'Thank you for registering'); // 세션플래쉬 메세지 설정
            Application::$app->response->redirect('/'); // 리다이렉트
        }
    /* ... */
}
``