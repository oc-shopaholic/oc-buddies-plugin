## Component "Registration"

You can choose an email template in the settings.

**Component properties:**
  - Mode (Submit form/Ajax form)
  - Send flash message (only for Ajax mode)
  - Enable redirect
  - Choose page for redirect (the URL of the page will be passed to the user ID)
  - Auto login user after registration
  - Activation of user after registration:
    * Enable
    * Disable
    * Send activation email
  
**Usage:**
The component is used to process the user registration form.
Required fields are: email, password, password_confirmation.
To send an ajax request, you must use the **Registration::onAjax** method.

**Example 1 (ajax submit)

```html
[Registration]
mode = "ajax"
redirect_on = 1
redirect_page = "registration_success"
activation = "activation_on"
force_login = 1
==
```

```javascript
$('form').request('Registration::onAjax');
```

**Example 2 (Submit form).**

```html

[Registration]
mode = "submit"
redirect_on = 1
redirect_page = "registration_success"
activation = "activation_on"
force_login = 1
==

{% set arError = Registration.getErrorMessage %}
{% set arForm = Registration.getOldFormData %}

<form href="{{ 'registration'|page }}">
    <label for="field-email">Email</label>
    <input type="email" id="field-email" placeholder="Email" name="email" value="{{ arForm.email }}">
    {% if arError.message is not empty and arError.field == 'email' %}
        <p>{{ arError.message }}</p>
    {% endif %}
    
     <label for="field-company-name">Company name</label>
    <input type="text" id="field-company-name" placeholder="My company" name="property[company_name]" value="{{ arForm.property.company_name }}">
    
    <label for="field-password">Password</label>
    <input type="password" id="field-password" name="password">
    
    <label for="field-password-confirmation">Password confirmation</label>
    <input type="password" id="field-password-confirmation" name="password_confirmation">
    
    <button type="submit">Submit</button>
</form>
{% if arError.message is not empty %}
    <p>{{ arError.message }}</p>
{% endif %}

```

The Registration.getOldFormData method returns the filled form fields, if the form was sent and an error occurred.
The Registration.getErrorMessage method returns an error message if the form was sent and an error occurred.
```php
[
    'message' => 'Error message',
    'field'   => 'email',           //Field name, if there was a validation error
]
```

### onCheckEmail() method

The method adds the ability to check the availability of email

**Example (Send ajax request)**
```javascript

$.request('Registration::onCheckEmail', {
    data: {'email': $('input[name="email"]').val()},
    success: function(data) {
        if(data.status) {
            //Email is available
        } else {
            //Email is not available
        }
    }
});
```

### Event "lovata.buddies::mail.registration.template.name"
You can add additional fields in the email template.
By default, the 'lovata.buddies::mail.registration' template is used.
To integrate with the Translate plugin, you need to create templates for languages with suffix = language code.
For example:
  * 'lovata.buddies::mail.registration' - for default language
  * 'lovata.buddies::mail.registration_ru' - for language with code 'ru'
```php

Event::listen('lovata.buddies::mail.registration.template.data', function($obUser) {
    ...
    
    //Return array with addition fields
    return $arResult;
});
```

## Component "ActivationPage"
  
**Usage:**
The component is used to activate the user in the activation mode by the link in the sent email.

**Example**

```html

[ActivationPage]
slug = ":slug"
force_login = 1
redirect_on = 0
redirect_page = ""
==
```

## Component "Login"
**Component properties:**
  - Mode (Submit form/Ajax form)
  - Send flash message (only for Ajax mode)
  - Enable redirect
  - Choose page for redirect (the URL of the page will be passed to the user ID)
  
**Usage:**
The component is used to process the user authorization form.
To send an ajax request, you must use the **Login::onAjax** method.

**Example 1 (ajax submit)

```html
[Login]
mode = "ajax"
redirect_on = 1
redirect_page = "index"
==
```

```javascript
$('form').request('Login::onAjax');
```

**Example 2 (Submit form)**

```html

[Login]
mode = "submit"
redirect_on = 1
redirect_page = "index"
==

{% set arError = Login.getErrorMessage %}
{% set arForm = Login.getOldFormData %}

<form href="{{ 'login'|page }}">
    <label for="field-email">Login</label>
    <input type="email" id="field-email" placeholder="Email" name="email" value="{{ arForm.email }}">
    {% if arError.message is not empty and arError.field == 'email' %}
        <p>{{ arError.message }}</p>
    {% endif %}
    
    <label for="field-password">Password</label>
    <input type="password" id="field-password" name="password">
    
    <label for="field-remember_me">Remember me</label>
    <input type="checkbox" id="field-remember_me" name="remember_me">
    
    <button type="submit">Submit</button>
</form>
{% if arError.message is not empty %}
    <p>{{ arError.message }}</p>
{% endif %}
```

The Login.getOldFormData method returns the filled form fields, if the form was sent and an error occurred.
The Login.getErrorMessage method returns an error message if the form was sent and an error occurred.
```php
[
    'message' => 'Error message',
    'field'   => 'email',           //Field name, if there was a validation error
]
```

### onSocialiteLogin() method

Redirect to social login page

**Example (Send ajax request)**
```javascript

$.request('Login::onSocialiteLogin', {
    data: {'driver': 'facebook'}
});
```

## Integration with [laravel/socialite](https://github.com/laravel/socialite) package

Documentation for Socialite can be found on the [Laravel website](https://laravel.com/docs/5.7/socialite) or [Socialite Providers](https://socialiteproviders.github.io/) site.

### Installation guide

You need to add [laravel/socialite](https://github.com/laravel/socialite) package and other socialite packages from [Socialite Providers](https://socialiteproviders.github.io/) list to your composer.json file.

```
{
    "require": [
        ...
        "laravel/socialite": "^3.1",
        "socialiteproviders/vkontakte": "^4.0",
        "socialiteproviders/instagram": "^3.0",
    ],
```

Execute below at the root of your project.
```
composer update
```
You can also install only packages and its dependencies without updating other packages by specifying the package.
```
composer require laravel/socialite
```

### Registration Socialite service provider

You need to register service provider class in your config/app.php file.

```php
'providers' => array_merge(include(base_path('modules/system/providers.php')), [
    // 'Illuminate\Html\HtmlServiceProvider', // Example
    
    'System\ServiceProvider',
    \Laravel\Socialite\SocialiteServiceProvider::class,
]),


'aliases' => array_merge(include(base_path('modules/system/aliases.php')), [

    // 'Str' => 'Illuminate\Support\Str', // Example
    'Socialite' => Laravel\Socialite\Facades\Socialite::class,
]),
``` 

### Integration with socialite package from [Socialite Providers](https://socialiteproviders.github.io/) list.

For example: integration with [socialiteproviders/instagram](https://socialiteproviders.github.io/providers/instagram.html) package.
1. Install package
```
composer require socialiteproviders/instagram
```
2. Remove Laravel\Socialite\SocialiteServiceProvider from your providers[] array in config\app.php if you have added it already. 
Add \SocialiteProviders\Manager\ServiceProvider::class to your providers[] array in config\app.php.
```php
'providers' => array_merge(include(base_path('modules/system/providers.php')), [
    // 'Illuminate\Html\HtmlServiceProvider', // Example
    
    'System\ServiceProvider',
    \SocialiteProviders\Manager\ServiceProvider::class,
]),
3. Add event listener
```php
\Event::listen(\SocialiteProviders\Manager\SocialiteWasCalled::class, 'SocialiteProviders\\Instagram\\InstagramExtendSocialite@handle');
```

### Usage example

1. Add facebook login button in your login page.
```javascript
$.request('Login::onSocialiteLogin', {data: {'driver': 'facebook'}});
``` 
2. Create a blank page and attach a component to it.
```html
title = "Facebook auth"
url = "/login/facebook"
is_hidden = 0

[SocialiteLogin]
code = "facebook"
redirect_success_page = "personal-data"
redirect_cancel_page = "login"
==
```


## Component "Logout"
**Component properties:**
  - Mode (Submit form/Ajax form)
  - Enable redirect
  - Choose page for redirect (the URL of the page will be passed to the user ID)
  
**Usage:**
The component is used for logout the user.
To send an ajax request, you must use the **Logout::onAjax** method.

**Example 1 (ajax submit)

```html
[Logout]
mode = "ajax"
redirect_on = 1
redirect_page = "index"
==
```

```javascript
$.request('Logout::onAjax');
```

**Example 2 (Submit form)**

```html

[Logout]
mode = "submit"
redirect_on = 1
redirect_page = "index"
==
```

## Component "ChangePassword"
**Component properties:**
  - Mode (Submit form/Ajax form)
  - Send flash message (only for Ajax mode)
  - Enable redirect
  - Choose page for redirect (the URL of the page will be passed to the user ID)
  - Enable / disable the old user password
  
**Usage:**
The component is used to process the user password change form.
Required fields are: old_password, password, password_confirmation.
To send an ajax request, you must use the **ChangePassword::onAjax** method.

**Example 1 (ajax submit)

```html
[ChangePassword]
mode = "ajax"
redirect_on = 1
redirect_page = "index"
check_old_password = 1
```

```javascript
$('form').request('ChangePassword::onAjax');
```

**Example 2 (Submit form)**

```html
[ChangePassword]
mode = "submit"
redirect_on = 1
redirect_page = "index"
check_old_password = 1
==

{% set arError = ChangePassword.getErrorMessage %}

<form href="{{ 'change_password'|page }}">
    
    <label for="field-old-password">Old password</label>
    <input type="password" id="field-old-password" name="old_password">
    
    <label for="field-password">Password</label>
    <input type="password" id="field-password" name="password">
    
    <label for="field-password-confirmation">Password confirmation</label>
    <input type="password" id="field-password-confirmation" name="password_confirmation">
    
    <button type="submit">Submit</button>
</form>
{% if arError.message is not empty %}
    <p>{{ arError.message }}</p>
{% endif %}
```

The ChangePassword.getOldFormData method returns the filled form fields, if the form was sent and an error occurred.
The ChangePassword.getErrorMessage method returns an error message if the form was sent and an error occurred.
```php
[
    'message' => 'Error message',
    'field'   => 'email',           //Field name, if there was a validation error
]
```

## Component "RestorePassword"
**Component properties:**
  - Mode (Submit form/Ajax form)
  - Send flash message (only for Ajax mode)
  
**Usage:**
The component is used to process the user password recovery form and send the email.
Required fields are: email.
To send an ajax request, you must use the **RestorePassword::onAjax** method.

**Example 1 (ajax submit)

```html
[RestorePassword]
mode = "ajax"
```

```javascript
$('form').request('RestorePassword::onAjax');
```

**Example 2 (Submit form)**

```html
[RestorePassword]
mode = "submit"
==

{% set arError = RestorePassword.getErrorMessage %}

<form href="{{ 'restore_password'|page }}">
    
    <label for="field-email">Email</label>
    <input type="email" id="field-email" placeholder="Email" name="email" value="{{ arForm.email }}">
    {% if arError.message is not empty and arError.field == 'email' %}
        <p>{{ arError.message }}</p>
    {% endif %}
    
    <button type="submit">Submit</button>
</form>
{% if arError.message is not empty %}
    <p>{{ arError.message }}</p>
{% endif %}
```

The RestorePassword.getOldFormData method returns the filled form fields, if the form was sent and an error occurred.
The RestorePassword.getErrorMessage method returns an error message if the form was sent and an error occurred.
```php
[
    'message' => 'Error message',
    'field'   => 'email',           //Field name, if there was a validation error
]
```

### Event "lovata.buddies::mail.restore.template.data"
You can add additional fields in the email template.
By default, the 'lovata.buddies::mail.restore' template is used.
To integrate with the Translate plugin, you need to create templates for languages with suffix = language code.
For example:
  * 'lovata.buddies::mail.restore' - for default language
  * 'lovata.buddies::mail.restore_ru' - for language with code 'ru'
```php

Event::listen('lovata.buddies::mail.restore.template.data', function($obUser) {
    ...
    
    //Return array with addition fields
    return $arResult;
});
```

## Component "ResetPassword"
**Component properties:**
  - Mode (Submit form/Ajax form)
  - Send flash message (only for Ajax mode)
  - Enable redirect
  - Choose page for redirect (the URL of the page will be passed to the user ID)
  
**Usage:**
The component is used to process the user password reset form.
Required fields are: password, password_confirmation.
To send an ajax request, you must use the **ResetPassword::onAjax** method.

**Example 1 (ajax submit)

```html
[ResetPassword]
mode = "ajax"
redirect_on = 1
redirect_page = "index"
```

```javascript
$('form').request('ResetPassword::onAjax');
```

**Example 2 (Submit form)**

```html
[ResetPassword]
mode = "submit"
redirect_on = 1
redirect_page = "index"
==

{% set arError = ResetPassword.getErrorMessage %}

<form href="{{ 'reset_password'|page }}">
    
    <label for="field-password">Password</label>
    <input type="password" id="field-password" name="password">
    
    <label for="field-password-confirmation">Password confirmation</label>
    <input type="password" id="field-password-confirmation" name="password_confirmation">
    
    <button type="submit">Submit</button>
</form>
{% if arError.message is not empty %}
    <p>{{ arError.message }}</p>
{% endif %}
```

The ResetPassword.getOldFormData method returns the filled form fields, if the form was sent and an error occurred.
The ResetPassword.getErrorMessage method returns an error message if the form was sent and an error occurred.
```php
[
    'message' => 'Error message',
    'field'   => 'email',           //Field name, if there was a validation error
]
```

## Component "UserPage"
**Component properties:**
  - Mode (Submit form/Ajax form)
  - Send flash message (only for Ajax mode)
  - Enable redirect
  - Choose page for redirect (the URL of the page will be passed to the user ID)
  
**Usage:**
The component is used to process the user data update form.
To send an ajax request, you must use the **UserPage::onAjax** method.

**Example 1 (ajax submit)

```html
[UserPage]
mode = "ajax"
redirect_on = 0
login_page = "login"
```

```javascript
$('form').request('UserPage::onAjax');
```

**Example 1 (Submit form).**

```html
[UserPage]
mode = "submit"
redirect_on = 1
redirect_page = "user_page"
login_page = "login"
==

{% set arError = UserPage.getErrorMessage %}
{% set arForm = UserPage.getOldFormData %}

<form href="{{ 'user_page'|page }}">
    <label for="field-email">Email</label>
    <input type="email" id="field-email" placeholder="Email" name="email" value="{{ arForm.email }}">
    {% if arError.message is not empty and arError.field == 'email' %}
        <p>{{ arError.message }}</p>
    {% endif %}
    
     <label for="field-company-name">Company name</label>
    <input type="text" id="field-company-name" placeholder="My company" name="property[company_name]" value="{{ arForm.property.company_name }}">
    
    <button type="submit">Submit</button>
</form>
{% if arError.message is not empty %}
    <p>{{ arError.message }}</p>
{% endif %}

```

The UserPage.getOldFormData method returns the filled form fields, if the form was sent and an error occurred.
The UserPage.getErrorMessage method returns an error message if the form was sent and an error occurred.
```php
[
    'message' => 'Error message',
    'field'   => 'email',           //Field name, if there was a validation error
]
```

## Component "UserData"
  
**Usage:**
The component returns the data of the authorized user.
The method "get" returns object of UserItem class.

**Example**

```html

[UserData]
==

{% set obUser = UserData.get %}

{% if obUser.isNotEmpty %}
<div>
    {{ obUser.name }} {{ obUser.last_name }}
</div>
{% else %}
<div>
    <button>Login</button>
</div>
{% endif %}
```

## UserItem class

The class allows to work with a cached data array of User model.

The UserItem class is extended from [ElementItem](https://github.com/lovata/oc-toolbox-plugin/wiki/ElementItem) class.

### Field list
  * (int) **id**
  * (string) **email**
  * (string) **name**
  * (string) **last_name**
  * (string) **middle_name**
  * (string) **phone**
  * (array) **phone_list**
  * (array) **property**
  * (array) **avatar** - array with file list ([see](https://github.com/kharanenka/oc-data-file-model)).
  
## Extending of login query

```php
User::extend(function ($obUser) {
        $obUser->addDynamicMethod('scopeExtendLoginQuery', function ($query, $credential, $value) use ($obUser) {
            if ($credential == 'email') {
                $query = $query->orWhere('login', $value);
            }

            return $query;
        });
    });
```