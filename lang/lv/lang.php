<?php return [
    'plugin' => [
        'name'                  => 'Lietotāji',
        'description'           => 'Users login/registration',
    ],
    'field' => [
        'name'                           => 'Vārds',
        'last_name'                      => 'Uzvārds',
        'middle_name'                    => 'Middle name',
        'password_confirm'               => 'Parole vēlreiz',
        'password_change'                => 'Mainīt paroli',
        'registration_mail_template'     => 'Registration mail template',
        'restore_password_mail_template' => 'Restore password mail template',
    ],
    'menu' => [
        'main'     => 'Lietotāji',
        'user'     => 'Lietotāji',
        'property' => 'Addition properties',
    ],
    'user' => [
        'name'          => 'lietotājs',
        'list_title'    => 'Lietotāju saraksts',
    ],
    'property' => [
        'name'       => 'property',
        'list_title' => 'Property list',
    ],
    'tab' => [
        'data'          => 'Data',
        'permissions'   => 'Labot lietotājus',
    ],
    'component' => [
        'registration'                  => 'Reģistrācija',
        'registration_desc'             => '',

        'login'                         => 'Autorizācija',
        'login_desc'                    => '',
        'socialite_login'               => 'Socialite login',
        'socialite_login_desc'          => '',
        'logout'                        => 'Logout',
        'logout_desc'                   => '',

        'user_page'                     => 'User page',
        'user_page_desc'                => '',
        'user_data'                     => 'User data',
        'user_data_desc'                => '',

        'activation_page'               => 'User activation page',
        'activation_page_desc'          => '',

        'reset_password'                => 'Reset password',
        'reset_password_desc'           => '',

        'change_password'               => 'Change password',
        'change_password_desc'          => '',

        'restore_password'         => 'Restore password',
        'restore_password_desc'    => '',


        'property_force_login'                  => 'User will be authorized after registration',
        'property_activation'                   => 'Lietotāju automātiska aktivācija',
        'property_activation_on'                => 'Ieslēgta',
        'property_activation_off'               => 'OFF',
        'property_activation_mail'              => 'Send activation email',
        'property_check_old_password'           => 'Check old password',
        'property_socialite_code'               => 'Socialite code',
        'property_login_page'                   => 'Redirect to login page if user is not authorized',
    ],
    'message' => [
        'e_user_create'                 => 'Error creating user',
        'e_user_banned'                 => 'User  ":user" is banned',
        'e_user_suspended'              => 'User ":user" is temporarily blocked.',
        'e_login_not_correct'           => 'You entered incorrect email or password.',
        'e_user_not_active'             => 'User is not activated.',
        'e_auth_fail'                   => 'You are already authorized.',
        'e_user_not_found'              => 'User ":user" not found',
        'e_check_old_password'          => 'Old password entered incorrectly.',
        'email_is_busy'                 => 'Ar e-patu :email kāds jau ir reģistrējies, mēģiniet autorizēties',
        'email_is_available'            => 'E-pasts :email nav pieejams',
        'registration_success'          => 'Reģistrācija pabeigta veiksmīgi',
        'user_update_success'           => 'Sniegtā informāciaj tika veiksmīgi saglabāta',
        'password_change_success'       => 'Parole tika veiksmīgi nomainīta.',
        'login_success'                 => 'Autorizācija noteika veiksmīgi.',
        'restore_mail_send_success'     => 'Sent email for password recovery.',
    ],
    'mail' => [
        'restore'      => 'Restore password',
        'registration' => 'Registration',
    ],
    'permission' => [
        'user'     => 'Manage users',
        'property' => 'Manage addition properties',
    ],
];