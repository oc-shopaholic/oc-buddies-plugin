<?php return [
    'plugin' => [
        'name'                  => 'Buddies',
        'description'           => 'Users login/registration',
    ],
    'field' => [
        'name'                           => 'Name',
        'last_name'                      => 'Last name',
        'middle_name'                    => 'Middle name',
        'password_confirm'               => 'Confirm password',
        'password_change'                => 'Change password',
        'registration_mail_template'     => 'Registration mail template',
        'restore_password_mail_template' => 'Restore password mail template',
    ],
    'menu' => [
        'main'     => 'Users',
        'user'     => 'Users',
        'property' => 'Addition properties',
    ],
    'user' => [
        'name'          => 'user',
        'list_title'    => 'User list',
    ],
    'property' => [
        'name'       => 'property',
        'list_title' => 'Property list',
    ],
    'tab' => [
        'data'          => 'Data',
        'permissions'   => 'Manage users',
    ],
    'component' => [
        'registration'                  => 'Registration',
        'registration_desc'             => '',

        'login'                         => 'Login',
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
        'property_activation'                   => 'User activation',
        'property_activation_on'                => 'ON',
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
        'email_is_busy'                 => 'Email :email is busy',
        'email_is_available'            => 'Email :email is available',
        'registration_success'          => 'You have successfully registered',
        'user_update_success'           => 'Your data was successfully saved',
        'password_change_success'       => 'Password was successfully changed.',
        'login_success'                 => 'You have been successfully authorized.',
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