<?php return [
    'plugin' => [
        'name'                  => 'Buddies',
        'description'           => 'Prijava/Registracija uporabnikov',
    ],
    'field' => [
        'name'                           => 'Ime',
        'last_name'                      => 'Priimek',
        'middle_name'                    => 'Drugo ime',
        'password_confirm'               => 'Potrdi geslo',
        'password_change'                => 'Spremeni geslo',
        'registration_mail_template'     => 'Predloga za registracijo',
        'restore_password_mail_template' => 'Predloga za obnovo gesla',
    ],
    'menu' => [
        'main'     => 'Uporabniki',
        'user'     => 'Uporabniki',
        'property' => 'Dodatne lastnosti',
    ],
    'user' => [
        'name'          => 'uporabnik',
        'list_title'    => 'Seznam uporabnikov',
    ],
    'property' => [
        'name'       => 'lastnost',
        'list_title' => 'Seznam lastnosti',
    ],
    'tab' => [
        'data'          => 'Podatki',
        'permissions'   => 'Upravljanje z uporabniki',
    ],
    'component' => [
        'registration'                  => 'Registracija',
        'registration_desc'             => '',

        'login'                         => 'Prijava',
        'login_desc'                    => '',
        'socialite_login'               => 'Prijava z socialite',
        'socialite_login_desc'          => '',
        'logout'                        => 'Odjava',
        'logout_desc'                   => '',

        'user_page'                     => 'Uporabniška stran',
        'user_page_desc'                => '',
        'user_data'                     => 'Uporabniški podatki',
        'user_data_desc'                => '',

        'activation_page'               => 'Uporabniška aktivacijska stran',
        'activation_page_desc'          => '',

        'reset_password'                => 'Ponastavi geslo',
        'reset_password_desc'           => '',

        'change_password'               => 'Spremeni geslo',
        'change_password_desc'          => '',

        'restore_password'              => 'Obnovi geslo',
        'restore_password_desc'         => '',


        'property_force_login'                  => 'Prijava uporabnika po registraciji',
        'property_activation'                   => 'Aktivacija uporabnika',
        'property_activation_on'                => 'DA',
        'property_activation_off'               => 'NE',
        'property_activation_mail'              => 'Pošlji aktivacijsko epošto',
        'property_check_old_password'           => 'Preveri staro geslo',
        'property_socialite_code'               => 'Socialite koda',
        'property_login_page'                   => 'Preusmeri na prijavno stran ko uporabnik ni prijavljen',
    ],
    'message' => [
        'e_user_create'                 => 'Napaka pri ustvarjanju uporabnika',
        'e_user_banned'                 => 'Uporabnik ":user" je onemogočen',
        'e_user_suspended'              => 'Uporabnik ":user" je začasno onemogočen.',
        'e_login_not_correct'           => 'Vnesli ste napačni email ali geslo.',
        'e_user_not_active'             => 'Uporabnik ni aktiviran.',
        'e_auth_fail'                   => 'Ste že prijavljeni.',
        'e_user_not_found'              => 'Uporabnika ":user" ni bilo mogoče najti',
        'e_check_old_password'          => 'Vpisano staro geslo nepravilno.',
        'email_is_busy'                 => 'Email :email ni na voljo',
        'email_is_available'            => 'Email :email je na voljo',
        'registration_success'          => 'Registracija uspešna',
        'user_update_success'           => 'Podatki so bili uspešno posodobljeni',
        'password_change_success'       => 'Sprememba gesla uspešna.',
        'login_success'                 => 'Prijava uspešna.',
        'restore_mail_send_success'     => 'Email za obnovo gesla poslan.',
    ],
    'mail' => [
        'restore'      => 'Obnovi geslo',
        'registration' => 'Registracija',
    ],
    'permission' => [
        'user'     => 'Upravljanje z uporabniki',
        'property' => 'Upravljanje z dodatnimi lastnostmi',
    ],
];