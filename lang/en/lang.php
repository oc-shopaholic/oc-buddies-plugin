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
        'property' => 'Additional properties',
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
        'socialite_login'               => 'Login via social media',
        'socialite_login_desc'          => '',
        'logout'                        => 'Logout',
        'logout_desc'                   => '',

        'user_page'                     => 'User page',
        'user_page_desc'                => 'User account page',
        'user_data'                     => 'User data',
        'user_data_desc'                => 'Authorized user page',

        'activation_page'               => 'User activation',
        'activation_page_desc'          => 'User activation page',

        'reset_password'                => 'Reset password',
        'reset_password_desc'           => '',

        'change_password'               => 'Change password',
        'change_password_desc'          => '',

        'restore_password'         => 'Restore password',
        'restore_password_desc'    => '',


        'property_force_login'                  => 'Auto login',
        'property_activation'                   => 'User activation',
        'property_activation_on'                => 'ON',
        'property_activation_off'               => 'OFF',
        'property_activation_mail'              => 'Send activation email',
        'property_check_old_password'           => 'Check the previous password',
        'property_socialite_code'               => 'Socialite code',
        'property_login_page'                   => 'Redirect to the login page if the user is not logged in',
    ],
    'message' => [
        'e_user_create'                 => 'Error creating user',
        'e_user_banned'                 => 'User ":user" is blocked',
        'e_user_suspended'              => 'User ":user" is temporarily blocked.',
        'e_login_not_correct'           => 'You have entered an incorrect email or password',
        'e_user_not_active'             => 'User suspended',
        'e_auth_fail'                   => 'You are already logged in.',
        'e_user_not_found'              => 'User ":user" not found',
        'e_check_old_password'          => 'Previous password is incorrect',
        'email_is_busy'                 => 'Email :email is busy',
        'email_is_available'            => 'Email :email is available',
        'registration_success'          => 'You have been successfully registered',
        'user_update_success'           => 'Your data was successfully saved',
        'password_change_success'       => 'Password was successfully changed.',
        'login_success'                 => 'You have been successfully logged in.',
        'restore_mail_send_success'     => 'Sent email for password recovery.',
    ],
    'mail' => [
        'restore'      => 'Restore password',
        'registration' => 'Registration',
    ],
    'permission' => [
        'user'     => 'Manage users',
        'property' => 'Manage additional properties',
    ],
];
