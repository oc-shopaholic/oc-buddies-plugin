# Plugin settings
    Backend -> Settings -> Shopaholic:
 1. **Validation settings:**
    - Настройка минимальной длинны пароля пользователя.
    - Настройка регулярного выражения для проверки пароля (Например: [a-zA-Z0-9]+)
 2. **Настройка отправки писем**
    - Отправка писем, исползуя механизм очередей, с указанием названия очереди для отправки писем.

# Component "Registration"
**Component properties:**
  - Режим работы компонента (Submit form/Ajax form)
  - Отправка Flash сообщения (только для Ajax режима)
  - Вкл. редиректа на страницу после успешной обработки формы
  - Настройка страницы для редиректа (в пареметры URL страницы будет передан ID пользователя - ['id' => 16])
  - Режим активации пользователя после регистрации:
    * Не активировать пользователя
    * Активировать пользователя
    * Отправить письмо со ссылкой для активации пользователя
  
**Usage:**
Компонент используется для обработки формы регистрации пользователя. Поля формы должны иметь
название формата: user[field] (Например user[email], user[property][company_name]). Обязательными полями
являются: email, password, password_confirmation.
Для отправки ajax запроса необходимо использовать метод **Registration::onAjax**.

**Example 1 (Submit form)**

```html 'page/registration.htm'

[Registration]
mode = "submit"
redirect_on = 1
redirect_page = "registration_success"
activation = "activation_on"
==

{% set arError = Registration.getErrorMessage %}
{% set arForm = Registration.getOldFormData %}

<form href="{{ 'registration'|page }}">
    <label for="field-email">Login</label>
    <input type="email" id="field-email" placeholder="Email" name="user[email]" value="{{ arForm.email }}">
    {% if arError.message is not empty and arError.field == 'email' %}
        <p>{{ arError.message }}</p>
    {% endif %}
    
     <label for="field-company-name">Company name</label>
    <input type="text" id="field-company-name" placeholder="My company" name="user[property][company_name]" value="{{ arForm.property.company_name }}">
    
    <label for="field-password">Password</label>
    <input type="password" id="field-password" name="user[password]">
    
    <label for="field-password-confirmation">Password confirmation</label>
    <input type="password" id="field-password-confirmation" name="user[password_confirmation]">
    
    <button type="submit">Submit</button>
</form>
{% if arError.message is not empty %}
    <p>{{ arError.message }}</p>
{% endif %}
```

Метод Registration.getOldFormData возвраает заполненные поля формы, если форма была отправлена и в возникла ошибка.
Метод Registration.getErrorMessage возвраает сообщение об ошибке, если форма была отправлена и возникла ошибка.
```php
[
    'message' => 'Error message',
    'field'   => 'email',           //Название поля, если возникла ошибка валидации
]
```

# Component "ActivationPage"
**Component properties:**
  - Название параметра в URL страницы для получения кода активации
  
**Usage:**
Компонент используется для активации пользователя в режиме активации по ссылке в отправленном письме.
Необходимо подключить компонент на страницу активации пользователя.

**Example**

```html 'page/activation_page.htm'

[ActivationPage]
slug = ":slug"
==
```

# Component "Login"
**Component properties:**
  - Режим работы компонента (Submit form/Ajax form)
  - Отправка Flash сообщения (только для Ajax режима)
  - Вкл. редиректа на страницу после успешной обработки формы
  - Настройка страницы для редиректа (в пареметры URL страницы будет передан ID пользователя - ['id' => 16])
  
**Usage:**
Компонент используется для обработки формы авторизации пользователя. Поля формы должны иметь
название формата: user[field] (Например user[email], user[password]).
Для отправки ajax запроса необходимо использовать метод **Login::onAjax**.

**Example 1 (Submit form)**

```html 'page/login.htm'

[Login]
mode = "submit"
redirect_on = 1
redirect_page = "index"
==

{% set arError = Login.getErrorMessage %}
{% set arForm = Login.getOldFormData %}

<form href="{{ 'login'|page }}">
    <label for="field-email">Login</label>
    <input type="email" id="field-email" placeholder="Email" name="user[email]" value="{{ arForm.email }}">
    {% if arError.message is not empty and arError.field == 'email' %}
        <p>{{ arError.message }}</p>
    {% endif %}
    
    <label for="field-password">Password</label>
    <input type="password" id="field-password" name="user[password]">
    
    <button type="submit">Submit</button>
</form>
{% if arError.message is not empty %}
    <p>{{ arError.message }}</p>
{% endif %}
```

Метод Login.getOldFormData возвраает заполненные поля формы, если форма была отправлена и в возникла ошибка.
Метод Login.getErrorMessage возвраает сообщение об ошибке, если форма была отправлена и возникла ошибка.
```php
[
    'message' => 'Error message',
    'field'   => 'email',           //Название поля, если возникла ошибка валидации
]
```

# Component "Logout"
**Component properties:**
  - Режим работы компонента (Submit form/Ajax form)
  - Вкл. редиректа на страницу после успешной обработки формы
  - Настройка страницы для редиректа (в пареметры URL страницы будет передан ID пользователя - ['id' => 16])
  
**Usage:**
Компонент используется для логаута пользователя.
Для отправки ajax запроса необходимо использовать метод **Logout::onAjax**.

**Example 1 (Submit form)**

```html 'page/logout.htm'

[Logout]
mode = "submit"
redirect_on = 1
redirect_page = "index"
==
```

# Component "ChangePassword"
**Component properties:**
  - Название параметра в URL страницы для получения ID пользователя
  - Режим работы компонента (Submit form/Ajax form)
  - Отправка Flash сообщения (только для Ajax режима)
  - Вкл. редиректа на страницу после успешной обработки формы
  - Настройка страницы для редиректа (в пареметры URL страницы будет передан ID пользователя - ['id' => 16])
  - Включение/отключение прокерки старого пароля пользователя
  
**Usage:**
Компонент используется для обработки формы изменения пароля пользователя. Поля формы должны иметь
название формата: user[field] (Например user[old_password], user[password]). Обязательными полями
являются: old_password, password, password_confirmation.
Для отправки ajax запроса необходимо использовать метод **ChangePassword::onAjax**.

**Example 1 (Submit form)**

```html 'page/change_password.htm'

[ChangePassword]
mode = "submit"
redirect_on = 1
redirect_page = "index"
check_old_password = 1
==

{% set arError = ChangePassword.getErrorMessage %}

<form href="{{ 'change_password'|page }}">
    
    <label for="field-old-password">Old password</label>
    <input type="password" id="field-old-password" name="user[old_password]">
    
    <label for="field-password">Password</label>
    <input type="password" id="field-password" name="user[password]">
    
    <label for="field-password-confirmation">Password confirmation</label>
    <input type="password" id="field-password-confirmation" name="user[password_confirmation]">
    
    <button type="submit">Submit</button>
</form>
{% if arError.message is not empty %}
    <p>{{ arError.message }}</p>
{% endif %}
```

Метод Login.getErrorMessage возвраает сообщение об ошибке, если форма была отправлена и возникла ошибка.
```php
[
    'message' => 'Error message',
    'field'   => 'email',           //Название поля, если возникла ошибка валидации
]
```
