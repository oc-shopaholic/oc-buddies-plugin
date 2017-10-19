<?php return [
    'plugin' => [
        'name'                  => 'Buddies',
        'description'           => 'Авторизация/регистрация пользователей',
    ],
    'field' => [
        'name'                => 'Имя',
        'last_name'           => 'Фамилия',
        'middle_name'         => 'Отчество',
        'password_confirm'    => 'Повторить пароль',
        'password_change'     => 'Изменить пароль',
        'queue_on'            => 'Отправка писем используя Queue',
        'queue_name'          => 'Название queue для отправки письма',
    ],
    'menu' => [
        'main'          => 'Пользователи',
        'user'          => 'Пользователи',
        'property'      => 'Дополнительные свойства',
    ],
    'user' => [
        'name'       => 'пользователя',
        'list_title' => 'Список пользователей',
    ],
    'property' => [
        'name'       => 'свойства',
        'list_title' => 'Список свойств',
    ],
    'tab' => [
        'data'          => 'Данные',
        'mail'          => 'Отправка писем',
        'permissions'   => 'Управление пользователями',
    ],
    'component' => [
        'registration'      => 'Регистрация',
        'registration_desc' => '',

        'login'       => 'Авторизация',
        'login_desc'  => '',
        'logout'      => 'logout',
        'logout_desc' => '',

        'user_page'      => 'Страница пользователя',
        'user_page_desc' => 'Страница личного кабинета пользователя',
        'user_data'      => 'Данные пользователя',
        'user_data_desc' => 'Данные авторизованного пользователя',

        'activation_page'      => 'Активация пользователя',
        'activation_page_desc' => 'Страница активации пользователя',

        'reset_password'      => 'Сброс пароля',
        'reset_password_desc' => '',

        'change_password'      => 'Изменение пароля',
        'change_password_desc' => '',

        'restore_password'      => 'Восстановление пароля',
        'restore_password_desc' => '',

        'property_force_login'        => 'Автоматическая авторизация',
        'property_activation'         => 'Активация пользователя',
        'property_activation_on'      => 'Включена',
        'property_activation_off'     => 'Выключена',
        'property_activation_mail'    => 'Отправить письмо подтвеждения email адреса',
        'property_check_old_password' => 'Проверять действующий пароль на совпадение',


    ],
    'message' => [
        'e_user_create'                 => 'Ошибка при создании пользователя',
        'e_user_banned'                 => 'Пользователь ":user" заблокирован',
        'e_user_suspended'              => 'Пользователь ":user" временно заблокирован',
        'e_login_not_correct'           => 'Некорректно введен email или пароль',
        'e_user_not_active'             => 'Пользователь деактивирован',
        'e_auth_fail'                   => 'Вы уже авторизованы',
        'e_user_not_found'              => 'Пользователь ":user" не найден',
        'e_check_old_password'          => 'Старый пароль введен не верно',
        'registration_success'          => 'Вы успешно зарегестировались',
        'password_change_success'       => 'Пароль успешно изменен',
        'login_success'                 => 'Вы успешно авторизовались',
        'restore_mail_send_success'     => 'Письмо для восстановления пароля отправлено',
        'user_update_success'           => 'Профиль пользователя успещно обновлен',
    ],
    'mail' => [
        'restore'      => 'Восстановление пароля',
        'registration' => 'Регистрация',
    ],
    'permission' => [
        'user'     => 'Управление пользователями',
        'property' => 'Управление свойствами полозователей',
        'settings'  => 'Управление настройками плагина',
    ],
];