<?php return [
    'plugin' => [
        'name'                  => 'Draudziņi',
        'description'           => 'Lietotāju autorizācija/reģistrācija',
    ],
    'field' => [
        'name'                           => 'Vārds',
        'last_name'                      => 'Uzvārds',
        'middle_name'                    => 'Otrais vārds',
        'password_confirm'               => 'Parole vēlreiz',
        'password_change'                => 'Mainīt paroli',
        'registration_mail_template'     => 'Reģistrācijas e-pasta šablons',
        'restore_password_mail_template' => 'Paroles atjaunošanas e-pasta šablons',
    ],
    'menu' => [
        'main'     => 'Lietotāji',
        'user'     => 'Lietotāji',
        'property' => 'Papildus rekvizīti',
    ],
    'user' => [
        'name'          => 'lietotājs',
        'list_title'    => 'Lietotāju saraksts',
    ],
    'property' => [
        'name'       => 'rekvizīts',
        'list_title' => 'Rekvizītu saraksts',
    ],
    'tab' => [
        'data'          => 'Dati',
        'permissions'   => 'Labot lietotājus',
    ],
    'component' => [
        'registration'                  => 'Reģistrācija',
        'registration_desc'             => '',

        'login'                         => 'Autorizācija',
        'login_desc'                    => '',
        'socialite_login'               => 'Autorizācija ar kādu no socijālajiem tīkiem',
        'socialite_login_desc'          => '',
        'logout'                        => 'Iziet',
        'logout_desc'                   => '',

        'user_page'                     => 'Lietotāja lapa',
        'user_page_desc'                => '',
        'user_data'                     => 'Lietotāja dati',
        'user_data_desc'                => '',

        'activation_page'               => 'Lietotāju aktivizācijas lapa',
        'activation_page_desc'          => '',

        'reset_password'                => 'Atiestatīt paroli',
        'reset_password_desc'           => '',

        'change_password'               => 'Mainīt paroli',
        'change_password_desc'          => '',

        'restore_password'         => 'Atjaunot paroli',
        'restore_password_desc'    => '',


        'property_force_login'                  => 'Lietotājs tiks automātisi autorizēts pēc reģistrācijas',
        'property_activation'                   => 'Lietotāju automātiska aktivācija',
        'property_activation_on'                => 'Ieslēgta/Jā',
        'property_activation_off'               => 'Izslēgta/Nē',
        'property_activation_mail'              => 'Nosūtīt lietotajam konta aktivisācijas e-pastu',
        'property_check_old_password'           => 'Pārbaudiet veco paroli',
        'property_socialite_code'               => 'Socialite kods',
        'property_login_page'                   => 'Pāradresēt lietotāju uz autorizācijas lapu, ja viņš nav autorizējies',
    ],
    'message' => [
        'e_user_create'                 => 'Notikusi kļūda izveidojot lietotāju',
        'e_user_banned'                 => 'Lietotājam ": user" pieeja ir liegta',
        'e_user_suspended'              => 'Lietotājs ":user" ir īslaicīgi bloķēts.',
        'e_login_not_correct'           => 'Jūs ievadījāt nepareizu e-pastu vai paroli.',
        'e_user_not_active'             => 'Jūsu lietotājs nav aktivizēts.',
        'e_auth_fail'                   => 'Jūs jau esat reģistrējies.',
        'e_user_not_found'              => 'Lietotājs ":user" netika atrasts',
        'e_check_old_password'          => 'Vecā paraole ievadīta nepareizi.',
        'email_is_busy'                 => 'Ar e-patu :email kāds jau ir reģistrējies, mēģiniet autorizēties',
        'email_is_available'            => 'E-pasts :email nav pieejams',
        'registration_success'          => 'Reģistrācija pabeigta veiksmīgi',
        'user_update_success'           => 'Sniegtā informāciaj tika veiksmīgi saglabāta',
        'password_change_success'       => 'Parole tika veiksmīgi nomainīta.',
        'login_success'                 => 'Autorizācija noteika veiksmīgi.',
        'restore_mail_send_success'     => 'Jums tika nosūtīts e-pats ar saiti, lai atjaunotu paroli.',
    ],
    'mail' => [
        'restore'      => 'Paroles atiestatīšana',
        'registration' => 'Reģistrācija',
    ],
    'permission' => [
        'user'     => 'Labot lietotāju',
        'property' => 'Pārvaldīt papildus rekvizītus',
    ],
];