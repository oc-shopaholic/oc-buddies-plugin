<?php return [
    'plugin'     => [
        'name'        => 'Buddies',
        'description' => 'Είσοδος/εγγραφή χρηστών',
    ],
    'field'      => [
        'name'                           => 'Όνομα',
        'last_name'                      => 'Επώνυμο',
        'middle_name'                    => 'Μεσαίο όνομα',
        'password_confirm'               => 'Επιβεβαιώστε τον κωδικό πρόσβασης',
        'password_change'                => 'Αλλαγή κωδικού πρόσβασης',
        'registration_mail_template'     => 'Πρότυπο αλληλογραφίας εγγραφής',
        'restore_password_mail_template' => 'Επαναφέρετε το πρότυπο αλληλογραφίας κωδικού πρόσβασης',
    ],
    'menu'       => [
        'main'     => 'Χρήστες',
        'user'     => 'Χρήστες',
        'property' => 'Πρόσθετες ιδιότητες',
    ],
    'user'       => [
        'name'       => 'χρήστης',
        'list_title' => 'Λίστα χρηστών',
    ],
    'property'   => [
        'name'       => 'ιδιοκτησία',
        'list_title' => 'Κατάλογος ακινήτων',
    ],
    'tab'        => [
        'data'        => 'Δεδομένα',
        'permissions' => 'Διαχείριση χρηστών',
    ],
    'component'  => [
        'registration'      => 'Εγγραφή',
        'registration_desc' => '',

        'login'                => 'Είσοδος',
        'login_desc'           => '',
        'socialite_login'      => 'Είσοδος μέσω social media',
        'socialite_login_desc' => '',
        'logout'               => 'logout',
        'logout_desc'          => '',

        'user_page'      => 'Σελίδα χρήστη',
        'user_page_desc' => 'Σελίδα λογαριασμού χρήστη',
        'user_data'      => 'Στοιχεία χρήστη',
        'user_data_desc' => 'Εξουσιοδοτημένη σελίδα χρήστη',

        'activation_page'      => 'Ενεργοποίηση χρήστη',
        'activation_page_desc' => 'Σελίδα ενεργοποίησης χρήστη',

        'reset_password'      => 'Επαναφορά κωδικού πρόσβασης',
        'reset_password_desc' => '',

        'change_password'      => 'Αλλαγή κωδικού πρόσβασης',
        'change_password_desc' => '',

        'restore_password'      => 'Επαναφορά κωδικού πρόσβασης',
        'restore_password_desc' => '',

        'property_force_login'        => 'Αυτόματη σύνδεση',
        'property_activation'         => 'Ενεργοποίηση χρήστη',
        'property_activation_on'      => 'ΟΝ',
        'property_activation_off'     => 'OFF',
        'property_activation_mail'    => 'Αποστολή email ενεργοποίησης',
        'property_check_old_password' => 'Ελέγξτε τον προηγούμενο κωδικό πρόσβασης',
        'property_socialite_code'     => 'Socialite code',
        'property_login_page'         => 'Ανακατεύθυνση στη σελίδα σύνδεσης εάν ο χρήστης δεν είναι συνδεδεμένος',
    ],
    'message'    => [
        'e_user_create'             => 'Σφάλμα δημιουργίας χρήστη',
        'e_user_banned'             => 'Ο χρήστης ":user" είναι αποκλεισμένος',
        'e_user_suspended'          => 'Ο χρήστης ":user" είναι προσωρινά αποκλεισμένος.',
        'e_login_not_correct'       => 'Έχετε εισαγάγει λανθασμένο email ή κωδικό πρόσβασης.',
        'e_user_not_active'         => 'Ο χρήστης τέθηκε σε αναστολή.',
        'e_auth_fail'               => 'Έχετε ήδη συνδεθεί.',
        'e_user_not_found'          => 'Ο χρήστης ":user" δεν βρέθηκε',
        'e_check_old_password'      => 'Ο προηγούμενος κωδικός πρόσβασης είναι λάθος.',
        'email_is_busy'             => 'Email :email έχει ληφθεί',
        'email_is_available'        => 'Email :email είναι διαθέσιμο',
        'registration_success'      => 'Έχετε εγγραφεί επιτυχώς',
        'password_change_success'   => 'Τα δεδομένα σας αποθηκεύτηκαν με επιτυχία',
        'login_success'             => 'Ο κωδικός πρόσβασης άλλαξε με επιτυχία',
        'restore_mail_send_success' => 'Έχετε συνδεθεί επιτυχώς',
        'user_update_success'       => 'Έστειλε email για ανάκτηση κωδικού πρόσβασης.',
    ],
    'mail'       => [
        'restore'      => 'Επαναφορά κωδικού πρόσβασης',
        'registration' => 'Εγγραφή',
    ],
    'permission' => [
        'user'     => 'Διαχείριση χρηστών',
        'property' => 'Διαχείριση ιδιοτήτων προσθήκης',
    ],
];
