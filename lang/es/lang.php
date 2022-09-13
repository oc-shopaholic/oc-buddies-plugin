<?php return [
    'plugin'     => [
        'name'        => 'Buddies',
        'description' => 'Autorización/registro de usuarios',
    ],
    'field'      => [
        'name'                           => 'Nombre',
        'last_name'                      => 'Primer apellido',
        'middle_name'                    => 'Segundo apellido',
        'password_confirm'               => 'Repetir la contraseña',
        'password_change'                => 'Cambiar la contraseña',
        'registration_mail_template'     => 'Plantilla de confirmación de registro',
        'restore_password_mail_template' => 'Plantilla de recuperación de contraseña',
    ],
    'menu'       => [
        'main'     => 'Usuarios',
        'user'     => 'Usuarios',
        'property' => 'Propiedades adicionales',
    ],
    'user'       => [
        'name'       => 'de usuario',
        'list_title' => 'Listado de usuarios',
    ],
    'property'   => [
        'name'       => 'propiedades',
        'list_title' => 'Listado de propiedades',
    ],
    'tab'        => [
        'data'        => 'Datos',
        'permissions' => 'Administración de usuarios',
    ],
    'component'  => [
        'registration'      => 'Registro',
        'registration_desc' => '',

        'login'                => 'Autorización',
        'login_desc'           => '',
        'socialite_login'      => 'Autorización a través de redes sociales',
        'socialite_login_desc' => '',
        'logout'               => 'logout',
        'logout_desc'          => '',

        'user_page'      => 'Página de usuario',
        'user_page_desc' => 'Página de area personal de usuario',
        'user_data'      => 'Datos de usuarios',
        'user_data_desc' => 'Datos de usuario autorizado',

        'activation_page'      => 'Activación de usuario',
        'activation_page_desc' => 'Página de activación de usuario',

        'reset_password'      => 'Restablecimiento de contraseña',
        'reset_password_desc' => '',

        'change_password'      => 'Cambio de contraseña',
        'change_password_desc' => '',

        'restore_password'      => 'Recuperación de contraseña',
        'restore_password_desc' => '',

        'property_force_login'        => 'Autorización automática',
        'property_activation'         => 'Activación de usuario',
        'property_activation_on'      => 'Encendida',
        'property_activation_off'     => 'Apagada',
        'property_activation_mail'    => 'Enviar una carta de confirmación de email',
        'property_check_old_password' => 'Comprobar contraseña actual para una coincidencia',
        'property_socialite_code'     => 'Socialite code',
        'property_login_page'         => 'Redirección de usuario a la página de inicio de sesión si no ha iniciado sesión',
    ],
    'message'    => [
        'e_user_create'             => 'Error al crear usuario',
        'e_user_banned'             => 'El usuario ":user" está bloqueado',
        'e_user_suspended'          => 'El usuario ":user" está temporalmente bloqueado',
        'e_login_not_correct'       => 'Email o contraseña está ingresado incorrectamente',
        'e_user_not_active'         => 'Usuario desactivado',
        'e_auth_fail'               => 'Ya está autorizado',
        'e_user_not_found'          => 'Usuario ":user" no encontrado',
        'e_check_old_password'      => 'Contraseña anterior se ingresó incorrectamente',
        'email_is_busy'             => 'Email :email ya está ocupado',
        'email_is_available'        => 'Email :email está disponible',
        'registration_success'      => 'Está registrado con éxito',
        'password_change_success'   => 'Cotraseña cambiada con éxito',
        'login_success'             => 'Ha iniciado sesión correctamente',
        'restore_mail_send_success' => 'Correo electrónico de recuperación de contraseña enviado',
        'user_update_success'       => 'Perfil de usuario actualizado con éxito',
    ],
    'mail'       => [
        'restore'      => 'Recuperación de contraseña',
        'registration' => 'Registro',
    ],
    'permission' => [
        'user'     => 'Administración de usuarios',
        'property' => 'Administración de propiedades de usuarios',
    ],
];
