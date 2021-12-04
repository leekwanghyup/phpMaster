## 뷰페이지에서 모델 객체 사용

- models 폴더 아래 RegisterModel.php 파일 생성 및 클래스 정의 
- 모델객체의 각각의 멤버변수에는 클라이언트가 전달한 파라미터의 값이 할당되어야한다. 
- 올바른 데이터가 입력 되기 위에 유효성 검사를 반드시 실시해야한다.
- 파라미터가 할당되고 유효성 검사가 끝나면 데이터베이스에 데이터가 삽입된다. 

> Model.php
```php
namespace app\models;
abstract class Model
{
    public function dataload(array $data)
    {
        // Request 객체에 저장된 파라미터를  모델객체에 할당한다.
    }

    public function validate()
    {
        // 유효성 검사
    }

    # 유효성 검사를 위한 규칙
    abstract public function rules(); 

    # 데이터베이스 등록
    abstract public function register();
}
```

> RegisterModel.php
```php
namespace app\models;

class RegisterModel extends Model
{
    public string $firstname; 
    public string $lastname; 
    public string $email; 
    public string $password; 
    public string $confirmPassword; 

    public function register()
    {
        # 데이터베이스 삽입
        echo "Create new user";
    }

    public function rules()
    {

    }
}
```

> AuthController.php
```php
public function register(Request $reqeust)
{
    $registerModel = new RegisterModel();
    if($reqeust->isPost()){
        $registerModel->dataload($reqeust->getBody()); // 전달받은 파라미터를 모델객체에 할당

        # 유효성 검사후 데이터베이스에 등록되면 Success 반환
        if($registerModel->validate() && $registerModel->register()){
            return "Success";
        }
        
        # 그렇지 않으면 다시 입력폼으로 이동, 이때 입력했던 값들을 모델객체에 저장하여 이동한다. 
        return $this->render('/register', ['model' => $registerModel]);
    }
    $this->setLayout('auth');
    return $this->render('register',['model' => $registerModel]);
}
```

> Model.php  dataload()메서드 구현
```php
public function dataload(array $data)
{
    foreach ($data as $key => $value)
    {
        if(property_exists($this, $key))  // 모델객체의 멤버변수의 이름과 파라미터의 키 값이 같으면
        {
            $this->$key = $value; 
        }
    }
}
```

<br>

- RegisterMdoel클래스의 validate() 메서드를 구현해보자
    + Model객체에서 폼 필드의 규칙을 상수로 선언한다.
    + 폼필드의 값이 타당하지 않을 경우 오류를 담을 배열을 만들고
    + 오류를 추가하는 메서드를 생성한다. 
    + 폼필드 마다 규칙을 기술한 rules메서드를 구현한다.

> Model.php
```php
public const RULE_REQUIRED = 'required'; 
public const RULE_EMAIL = 'email';
public const RULE_MIN = 'min';
public const RULE_MAX = 'max'; 
public const RULE_MATCH = 'match';

public array $errors = [];
```

<br>

> RegisterMdoel.php rules()메서드 구현
```php
public function rules()
{
    return [
        'firstname' => [ self::RULE_REQUIRED ],
        'lastname' => [ self::RULE_REQUIRED ],
        'email' => [ self::RULE_REQUIRED, self::RULE_EMAIL ],
        'password' => [ self::RULE_REQUIRED, [self::RULE_MIN, 'min'=> 8 ], [self::RULE_MAX, 'max'=>24] ],
        'confirmPassword' => [ self::RULE_REQUIRED, [self::RULE_MATCH, 'match' => 'password'] ]
    ];    
}
```

> validate() 메서드 구현 
```php
public function validate()
{
    // Flat Rule Name
    foreach($this->rules() as $attribute => $rules)
    {
        $value = $this->{$attribute}; 
        foreach ($rules as $rule) // $rules은 배열 또는 문자열이다. 
        {
            $ruleName = $rule; 
            if(is_array($rule)) // 배열인 경우 
            {   
                $ruleName = $rule[0]; 
            }
            if($ruleName === self::RULE_REQUIRED && !$value){
                $this->addError($attribute, self::RULE_REQUIRED);  // 오류를 추가할 메서드를 정의해야함
            }
        }
    }
    return empty($this->errors); 
}
```

<br>

> Model addError()
```php
public function addError(string $attribute, string $rule) 
{
    $this->errors[$attribute][] = $this->errorMessage()[$rule] ?? ''; // 에러메세지를 담을 메서드를 정의해야한다.
}
```

<br>

> Model addError()
```php
public function errorMessage() 
{
    return 
    [
        self::RULE_REQUIRED => 'This filed is required', 
        self::RULE_EMAIL => 'This field must be valid email address', 
        self::RULE_MIN => 'Min length of this field must be {min}', 
        self::RULE_MAX => 'Max length of this field must be {max}', 
        self::RULE_MATCH => 'This field must be the same as {match}', 
    ]; 
}
```

- 입력폼에서 어떠한 값도 입력하지말고 전송해보자.
> AuthController
```php
public function register(Request $reqeust)
{
    /* ... */
    if($registerModel->validate() && $registerModel->register()){
        return "Success";
    }
    dumping($registerModel->errors); // 배열에 담긴 에레메세지 확인
        
    /* ... */
}
```

- 나머지 규칙들을 검사하는 조건문을 작성한다. 
> Model validate()
```php
if($ruleName === self::RULE_REQUIRED && !$value)
{
    $this->addError($attribute, self::RULE_REQUIRED); 
}
if($ruleName === self::RULE_EMAIL && !filter_var($value, FILTER_VALIDATE_EMAIL))
{
    $this->addError($attribute, self::RULE_EMAIL); 
}
if($ruleName === self::RULE_MIN && strlen($value) < $rule['min']){
    $this->addError($attribute, self::RULE_MIN, $rule); // 메서드의 파라미터를 추가해야한다.
}
if($ruleName === self::RULE_MAX && strlen($value) > $rule['max']){
    $this->addError($attribute, self::RULE_MAX, $rule); // 메서드의 파라미터를 추가해야한다.
}
if($ruleName === self::RULE_MATCH && $value !== $this->{$rule['match']}){ // $this->{$rule['match']}
    $this->addError($attribute, self::RULE_MATCH, $rule);
}
```

> Model addError()
```php
public function addError(string $attribute, string $rule, $param=[]) 
{
    $message = $this->errorMessage()[$rule] ?? '';
    foreach($param as $key => $value){
        $message = str_replace("{{$key}}", $value, $message); 
    }
    $this->errors[$attribute][] = $message;
}
```

<br>

## 입력폼에 에러메세지 출력 

<br>

> Model hasError()
```php
public function hasError($attribute)
{
    return $this->errors[$attribute] ?? false;
}
```

<br>

> getFirstError()
```php
public function getFirstError($attribute)
{
    return $this->errors[$attribute][0] ?? false ; 
}
```

<br>

> register.php
```php
<?php 
    $firstname = $model->firstname ?? ''; 
    $lastname = $model->lastname ?? ''; 
    $email = $model->email ?? '';
    $password = $model->password ?? '';
    $confirmPassword = $model->confirmPassword ?? '';
?>

<h1>Register</h1>
<form action="/register" method="post">
    <table>
        <tr>
            <td>First Nmae</td>
            <td>
                <input type="text" name="firstname" value="<?= $firstname ?>" 
                    class="<?php echo $model->hasError('firstname') ? ' is-invalid' : '' ?>">
                <span class="invalid-feedback">
                    <?php echo $model->getFirstError('firstname') ?>
                </span>
            </td>
        </tr>
        <tr>
            <td>Last tNmae</td>
            <td>
                <input type="text" name="lastname" value="<?= $lastname ?>"
                    class="<?php echo $model->hasError('lastname') ? ' is-invalid' : '' ?>">
                <span class="invalid-feedback">
                    <?php echo $model->getFirstError('lastname') ?>
                </span>
            </td>
        </tr>
        <tr>
            <td>Email</td>
            <td>
                <input type="text" name="email" value="<?= $email ?>" 
                    class="<?php echo $model->hasError('firstname') ? ' is-invalid' : '' ?>">
                <span class="invalid-feedback">
                    <?php echo $model->getFirstError('email') ?>
                </span>
            </td>
        </tr>
        <tr>
            <td>password</td>
            <td>
                <input type="password" name="password" value="<?= $password ?>"
                class="<?php echo $model->hasError('firstname') ? ' is-invalid' : '' ?>"/>
                <span class="invalid-feedback">
                    <?php echo $model->getFirstError('password') ?>
                </span>
            </td>
        </tr>
        <tr>
            <td>Confirm Password</td>
            <td>
                <input type="password" name="confirmPassword" value="<?= $confirmPassword ?>"
                class="<?php echo $model->hasError('firstname') ? ' is-invalid' : '' ?>" />
                <span class="invalid-feedback">
                    <?php echo $model->getFirstError('confirmPassword') ?>
                </span>
            </td>
        </tr>
        <tr>
            <td><button>Submit</button></td>
        </tr>
    </table>
</form>

<style>
.is-invalid{border: 1px solid red;}
.invalid-feedback {font-size: 12px; color: red; }
</style>
```

<br>

## php로 폼태그 생성  37분 18초
- core/form폴더 아래  Form.php, Filed.php 파일 생성 후 클래스 정의
- 네임스페이스는 app\core\form으로 한다.

>Form.php 
```php
namespace app\core\form;

class Form 
{
    public static function begin($action, $method)
    {
        echo "<form action='$action' method='$method'>";
        return new Form(); 
    }

    public static function end()
    {
        return"</form>";
    }
}
```

<br>

```php
<?php 
use app\core\form\Form;
/*...*/
?>
<?php $form = Form::begin('/contact','post') ?>

<?php $form->end() ?>
```

- toString 메서드를 구현하면 객체생성시 해당 문자열을 반환한다. 
> Filed.php
```php
<?php 
namespace app\core\form;

class Field 
{
  public function __toString()
  {
    return "
    <tr>
        <td>First Name</td>
        <td>
            <input type='text' name='firstname' value='' >
        </td>
    </tr>
    "; 
  }  
}

?>
```

<br>

> Form.php
```php
public function field()
{
    return new Field(); // __toString()메서드가 구현하는 문자열을 반환한다. 
}
```

> register.php
```php
<?php $form = Form::begin('/register','post') ?>
<table>
<?php echo $form->field(); ?>
</table>
<button>Submit</button>
<?php $form->end() ?>
```

<br>

- 필드폼에서 동적으로 제어해야하는 부분이 있다. 
    + 각 필드의 이름을 나타내는 부분이 달라한다.
    + 각 필드의 name속성과 value속성이 달라야한다.
    + 오류를 피드백하는 부분이 달라야한다. 
```php
class Field 
{
    public Model $model;
    public string $attribute; 
    public string $fieldname; 

    public function __construct($model, $attribute,$fieldname)
    {
        $this->model = $model; 
        $this->attribute = $attribute;  
        $this->fieldname = $fieldname;
    }

    public function __toString()
    {
        $value = $this->model->{$this->attribute} ?? '';
        $hasError = $this->model->hasError($this->attribute) ? ' is-invalid' : '';
        $errorMessage = $this->model->getFirstError($this->attribute);
        return 
        "<tr>
            <td> {$this->fieldname}</td>
            <td>
                <input type='text' name='{$this->attribute}' value='{$value}' class='$hasError' >
                <span class='invalid-feedback'> $errorMessage </span></td>
            </td>
        </tr>"; 
    }  
}
```

- 필드는 Form에서 생성되므로 해당하는 파라미터를 전달해야한다. 
> Form.php
```php
public function field($model,$attribute,$filedname)
{
    return new Field($model,$attribute,$filedname); 
}
```
- 파라미터를 전달하고 나머지 폼필드도 같은 방식으로 만든다.
> register.php
```php
<?php echo $form->field($model,'firstname','First Name'); ?>
<?php echo $form->field($model,'lastname','Last Name'); ?>
<?php echo $form->field($model,'email','Email'); ?>
<?php echo $form->field($model,'password','Password'); ?>
<?php echo $form->field($model,'confirmPassword','Confirm Password'); ?>
```
- 해결해야할 문제점이 남았다.
    - 패스워드 필드가 일반 텍스트 필드를 사용하고 있다.
    - 이메일 필드를 이메일 형식으로 바꾸고자한다.

>Filed.php
```php
# 각각의 필드 타입을 상수로 정의 
public const TYPE_TEXT = 'text';
public const TYPE_PASSWORD = 'password';
public const TYPE_NUMBER = 'number';
public const TYPE_EMAIL = 'email';

# 멤버변수로 선언
public string $type; 
/* ... */

# 생성자에서 기본필드타입을 text로 초기화
public function __construct($model, $attribute,$fieldname)
{
    $this->type = self::TYPE_TEXT;
    /* ... */
}

public function __toString()
{
   /* ...  */
   # input 의 type을 이 필드의 멤버변수인 type으로 지정한다. 
    return 
    "<tr>
        <td> {$this->fieldname}</td>
        <td>
            <input type='{$this->type}' name='{$this->attribute}' value='{$value}' class='$hasError' >
            <span class='invalid-feedback'> $errorMessage </span></td>
        </td>
    </tr>"; 
}  


public function passwordField()
    {
        $this->type = self::TYPE_PASSWORD; 
        return $this; 
    }
public function emailField(){
    $this->type = self::TYPE_EMAIL;
    return $this; 
}

```

>
```php
<?php echo $form->field($model,'email','Email')->emailField(); ?>
<?php echo $form->field($model,'password','Password')->passwordField(); ?>
<?php echo $form->field($model,'confirmPassword','Confirm Password')->passwordField(); ?>
```



## 완성
> Form.php
```php
<?php 
namespace app\core\form;

use app\models\Model;

class Form 
{
    public static function begin($action, $method)
    {
        echo "<form action='$action' method='$method'>";
        return new Form();
    }

    public static function end()
    {
        echo "</form>";
    }

    public function field(Model $model, $attribute)
    {
        return new Field($model, $attribute);
    }
}
```

> Filed
```php
<?php 

namespace app\core\form;

use app\models\Model;

class Field
{
    public const TYPE_TEXT = 'text';
    public const TYPE_PASSWORD = 'password';
    public const TYPE_NUMBER = 'number';

    public string $type; 
    public Model $model; 
    public string $attribute; 

    public function __construct($model, $attribute)
    {
        $this->type = self::TYPE_TEXT;
        $this->model = $model; 
        $this->attribute = $attribute; 
    }

    public function __toString()
    {
        $value =  $this->model->{$this->attribute} ?? '';
        $hasError = $this->model->hasError($this->attribute) ? ' is-invalid' : '';
        $errorMessage = $this->model->getFirstError($this->attribute);
        return 
        "
        <tr>
            <td>$this->attribute</td>
            <td><input type='$this->type' name='$this->attribute' value='$value' class='$hasError'>
            <span class='invalid-feedback'> $errorMessage </span></td>
        </tr>
        ";
    }

    public function passwordField()
    {
        $this->type = self::TYPE_PASSWORD; 
        return $this; 
    }
}
?>
```

> register.php
```php
<?php
use app\core\form\Form;
?>

<h1>Register</h1>
<?php $form = Form::begin('','post') ?>
<table>
    <?php echo $form->field($model, 'firstname') ?>
    <?php echo $form->field($model, 'lastname') ?>
    <?php echo $form->field($model, 'email') ?>
    <?php echo $form->field($model, 'password')->passwordField() ?>
    <?php echo $form->field($model, 'confirmPassword')->passwordField() ?>
</table>
<button>Submit</button>
<?php Form::end() ?>



<style>
.is-invalid{border: 1px solid red;}
.invalid-feedback {font-size: 12px; color: red; }
</style>
```

