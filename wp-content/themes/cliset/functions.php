<?php
function clitheme_enqueue_styles()
{

    $cssurl = get_template_directory_uri() . '/assets/css/';
    wp_enqueue_style('clitheme-style', get_stylesheet_uri());
    wp_enqueue_style('clitheme-signup-style', $cssurl . 'signup.css');

    $jsurl = get_template_directory_uri() . '/assets/js/';
    wp_enqueue_script('customjs', $jsurl . 'custom.js', array('jquery'), null, true);

    wp_enqueue_script(
        'google-recaptcha',
        'https://www.google.com/recaptcha/api.js',
        array(),
        null,
        true
    );

}
add_action('wp_enqueue_scripts', 'clitheme_enqueue_styles');

function clitheme_setup()
{
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');

    register_nav_menus(array(
        'primary' => __('Primary Menu', 'clitheme'),
        'footer' => __('Footer other Link', 'clitheme'),
    ));
}
add_action('after_setup_theme', 'clitheme_setup');


function verify_recaptcha()
{

    if (empty($_POST['g-recaptcha-response'])) {
        return 'missing-input-response';
    }

    $secret = "6Lf8WJQsAAAAABi2aEbjDkWu64he_OX2wrjJ2HHU";
    $response = sanitize_text_field($_POST['g-recaptcha-response']);
    $remoteip = $_SERVER['REMOTE_ADDR'];

    $verify = wp_remote_post("https://www.google.com/recaptcha/api/siteverify", array(
        'body' => array(
            'secret' => $secret,
            'response' => $response,
            'remoteip' => $remoteip
        )
    ));


    if (is_wp_error($verify)) {
        return 'connection-error';
    }

    $body = json_decode(wp_remote_retrieve_body($verify));

    if (isset($body->success) && $body->success === true) {
        echo "<script>console.log('reCAPTCHA verified successfully');</script>";
        return true;
    } else {
       
        return isset($body->{'error-codes'}) ? implode(',', $body->{'error-codes'}) : 'unknown-error';
    }
}


// add_action('init', function () {


    if (isset($_POST['signup'])) {
        // if (!verify_recaptcha()) {
        //     echo "<script>alert('reCAPTCHA verification failed.');</script>";
        //     return;
        // }

        $fname = sanitize_text_field($_POST['first_name']);
        $lname = sanitize_text_field($_POST['last_name']);
        $username = sanitize_text_field($_POST['username']);
        $email = sanitize_email($_POST['email']);
        $password = sanitize_text_field($_POST['password']);
        $confirm = sanitize_text_field($_POST['confirm_password']);
        $gender = isset($_POST['radiogroup1']) ? sanitize_text_field($_POST['radiogroup1']) : '';
        $terms = isset($_POST['terms']) ? true : false;

        $errors = array();

        if (empty($fname))
            $errors['fname'] = "First Name is required.";
        if (empty($lname))
            $errors['lname'] = "Last Name is required.";
        if (empty($username))
            $errors['username'] = "Username is required.";
        if (!is_email($email))
            $errors['email'] = "Invalid email address.";
        if (strlen($password) < 6)
            $errors['password'] = "Password must be at least 6 characters.";
        if ($password !== $confirm)
            $errors['confirm'] = "Passwords do not match.";
        if (empty($gender))
            $errors['gender'] = "Gender selection is required.";
        if (!$terms)
            $errors['terms'] = "You must agree to the terms.";


        if (!empty($errors)) {
            foreach ($errors as $error) {
                echo "<p style='color:red;'><script>alert('$error');</script></p>";
            }
            return;
        }

        $userdata = array(
            'user_login' => $username,
            'user_pass' => $password,
            'user_email' => $email,
            'first_name' => $fname,
            'last_name' => $lname,
            'role' => 'subscriber'
        );

        $user_id = wp_insert_user($userdata);

        if (!is_wp_error($user_id)) {

            update_user_meta($user_id, 'gender', $gender);

            echo "<script>alert('User registered successfully!');</script>";
            wp_redirect(home_url('/sign-in/'));
            exit;
        } else {
            echo "<script>alert('Registration failed: " . $user_id->get_error_message() . "');</script>";
        }
    }

    if (isset($_POST['signin'])) {

        if (!verify_recaptcha()) {
            echo "<script>alert('reCAPTCHA verification failed.');</script>";
            return;
        }
        $login = sanitize_text_field($_POST['login']);
        $password = sanitize_text_field($_POST['password']);
        $remember = isset($_POST['remember']) ? true : false;


        if (is_email($login)) {
            $user = get_user_by('email', $login);
            if ($user) {
                $login = $user->user_login;
            }
        }

        $credentials = array(
            'user_login' => $login,
            'user_password' => $password,
            'remember' => $remember
        );

        $user = wp_signon($credentials, false);

        if (is_wp_error($user)) {
            echo '<div class="error-message">' . $user->get_error_message() . '</div>';
        } else {
            wp_redirect(home_url());
            exit;
        }
    }
// });


