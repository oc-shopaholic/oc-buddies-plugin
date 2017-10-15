<?php return [
    'plugin' => [
        'name'                  => 'Buddies',
        'description'           => 'Users login/registration',
    ],
    'field' => [
        'name'             => 'Name',
        'last_name'        => 'Last name',
        'middle_name'      => 'Middle name',
        'password_confirm' => 'Confirm password',
        'password_change'  => 'Change password',
        'queue_on'         => 'Sending messages from the queue',
        'queue_name'       => 'The name of the queue for sending the emails',
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
        'mail'          => 'Sending emails',
        'permissions'   => 'Manage users',
    ],
    'component' => [
        'registration'                  => 'Registration',
        'registration_desc'             => '',

        'login'                         => 'Login',
        'login_desc'                    => '',
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

        'property_redirect_page'                => 'Redirect page',
        'property_redirect_on'                  => 'Redirect ON',
        'property_flash_on'                     => 'Flash ON',
        'property_force_login'                  => 'User will be authorized after registration',
        'property_activation'                   => 'User activation',
        'property_activation_on'                => 'ON',
        'property_activation_off'               => 'OFF',
        'property_activation_mail'              => 'Send activation email',
        'property_check_old_password'           => 'Check old password',

        'property_mode'             => 'Component mode',
        'mode_submit'               => 'Form submit',
        'mode_ajax'                 => 'Ajax',
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
        'settings' => 'Manage settings',
    ],
];