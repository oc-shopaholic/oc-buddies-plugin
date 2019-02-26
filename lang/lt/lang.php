<?php return [
    'plugin' => [
        'name'                  => 'Draugai',
        'description'           => 'Vartotojų prisijungimas/registracija',
    ],
    'field' => [
        'name'                           => 'Vardas',
        'last_name'                      => 'Pavardė',
        'middle_name'                    => 'Vidurinis vardas',
        'password_confirm'               => 'Patvirtinkite slaptažodį',
        'password_change'                => 'Keisti slaptažodį',
        'registration_mail_template'     => 'Registracijos pašto šablonas',
        'restore_password_mail_template' => 'Atkurti slaptažodžio pašto šabloną',
    ],
    'menu' => [
        'main'     => 'Vartotojai',
        'user'     => 'Vartotojai',
        'property' => 'Papildomi parametrai',
    ],
    'user' => [
        'name'          => 'Vartotojas',
        'list_title'    => 'Vartotojų sąrašas',
    ],
    'property' => [
        'name'       => 'Parametrai',
        'list_title' => 'Parametrų sąrašas',
    ],
    'tab' => [
        'data'          => 'Duomenys',
        'permissions'   => 'Vartotojų tvarkymas',
    ],
    'component' => [
        'registration'                  => 'Registracija',
        'registration_desc'             => '',

        'login'                         => 'Prisijungti',
        'login_desc'                    => '',
        'socialite_login'               => 'Socialite prisijungimas',
        'socialite_login_desc'          => '',
        'logout'                        => 'Atsijungti',
        'logout_desc'                   => '',

        'user_page'                     => 'Vartotojo paskyra',
        'user_page_desc'                => '',
        'user_data'                     => 'Vartotojo duomenys',
        'user_data_desc'                => '',

        'activation_page'               => 'Vartotojo aktyvavimo puslapis',
        'activation_page_desc'          => '',

        'reset_password'                => 'Atstatyti slaptažodį',
        'reset_password_desc'           => '',

        'change_password'               => 'Keisti slaptažodį',
        'change_password_desc'          => '',

        'restore_password'         => 'Atstatyti slaptažodį',
        'restore_password_desc'    => '',


        'property_force_login'                  => 'Vartotojas bus prijungtas po registracijos',
        'property_activation'                   => 'Vartotojo aktyvavimas',
        'property_activation_on'                => 'ĮJUNGTA',
        'property_activation_off'               => 'Išjungta',
        'property_activation_mail'              => 'Siųsti patvirtinimo el. laišką',
        'property_check_old_password'           => 'Tikrinti senąjį slaptažodį',
        'property_socialite_code'               => 'Socialite kodas',
        'property_login_page'                   => 'Nukreipti į prisijungimo puslapį jei vartotojas nėra prisijungęs',
    ],
    'message' => [
        'e_user_create'                 => 'Klaida sukuriant vartotoją',
        'e_user_banned'                 => 'Vartotojas ":user" yra užblokuotas',
        'e_user_suspended'              => 'Vartotojas ":user" yra laikinai užblokuotas.',
        'e_login_not_correct'           => 'Įvedėte neteisingus prisijungimo duomenis.',
        'e_user_not_active'             => 'Vartotojas yra neaktyvuotas.',
        'e_auth_fail'                   => 'Esate jau prisijungę.',
        'e_user_not_found'              => 'Vartotojas ":user" nerastas',
        'e_check_old_password'          => 'Senas slaptažodis įvestas neteisingai.',
        'email_is_busy'                 => 'El.paštas :email jau yra naudojamas',
        'email_is_available'            => 'El.paštas :email yra galimas',
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