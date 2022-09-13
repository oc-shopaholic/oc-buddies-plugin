<?php return [
    'plugin'     => [
        'name'        => 'Buddies',
        'description' => 'Авторизація/реєстрація користувачів',
    ],
    'field'      => [
        'name'                           => "Ім'я",
        'last_name'                      => 'Прізвище',
        'middle_name'                    => 'По батькові',
        'password_confirm'               => 'Повторити пароль',
        'password_change'                => 'Змінити пароль',
        'registration_mail_template'     => 'Шаблон листа підтвердження реєстрації',
        'restore_password_mail_template' => 'Шаблон листа відновлення пароля',
    ],
    'menu'       => [
        'main'     => 'Користувачі',
        'user'     => 'Користувачі',
        'property' => 'Додаткові характеристики',
    ],
    'user'       => [
        'name'       => 'користувача',
        'list_title' => 'Список користувачів',
    ],
    'property'   => [
        'name'       => 'властивості',
        'list_title' => 'Список властивостей',
    ],
    'tab'        => [
        'data'        => 'Дані',
        'permissions' => 'Управління користувачами',
    ],
    'component'  => [
        'registration'      => 'Реєстрація',
        'registration_desc' => '',

        'login'                => 'Авторизация',
        'login_desc'           => '',
        'socialite_login'      => 'Авторизация через соц. мережі',
        'socialite_login_desc' => '',
        'logout'               => 'logout',
        'logout_desc'          => '',

        'user_page'      => 'Сторінка користувача',
        'user_page_desc' => 'Сторінка особистого кабінету користувача',
        'user_data'      => 'Дані користувача',
        'user_data_desc' => 'Дані авторизованого користувача',

        'activation_page'      => 'Активація користувача',
        'activation_page_desc' => 'Сторінка активації користувача',

        'reset_password'      => 'Скидання пароля',
        'reset_password_desc' => '',

        'change_password'      => 'Зміна пароля',
        'change_password_desc' => '',

        'restore_password'      => 'Відновлення пароля',
        'restore_password_desc' => '',

        'property_force_login'        => 'Автоматична авторизація',
        'property_activation'         => 'Активація користувача',
        'property_activation_on'      => 'Включена',
        'property_activation_off'     => 'Вимкнена',
        'property_activation_mail'    => 'Надіслати листа підтвердження email адреси',
        'property_check_old_password' => 'Перевіряти чинний пароль на збіг',
        'property_socialite_code'     => 'Socialite code',
        'property_login_page'         => 'Перенаправлення користувача на сторінку логіна, якщо він не авторизований',
    ],
    'message'    => [
        'e_user_create'             => 'Помилка при створенні користувача',
        'e_user_banned'             => 'Користувач ":user" заблокований',
        'e_user_suspended'          => 'Користувач ":user" тимчасово заблокований',
        'e_login_not_correct'       => 'Некоректно введено email або пароль',
        'e_user_not_active'         => 'Користувач деактивований',
        'e_auth_fail'               => 'Ви вже авторизовані',
        'e_user_not_found'          => 'Користувач ":user" не знайдений',
        'e_check_old_password'      => 'Старий пароль введений не правильно',
        'email_is_busy'             => 'Email :email вже зайнятий',
        'email_is_available'        => 'Email :email доступний',
        'registration_success'      => 'Ви успішно зареєструвалися',
        'password_change_success'   => 'Пароль успішно змінено',
        'login_success'             => 'Ви успішно авторизувалися',
        'restore_mail_send_success' => 'Лист відновлення пароля відправлено',
        'user_update_success'       => 'Профіль користувача успішно оновлено',
    ],
    'mail'       => [
        'restore'      => 'Відновлення пароля',
        'registration' => 'Реєстрація',
    ],
    'permission' => [
        'user'     => 'Управління користувачами',
        'property' => 'Управління властивостями користувачів',
    ],
];
