<?php
function clitheme_enqueue_styles()
{

    $cssurl = get_template_directory_uri() . '/assets/css/';
    wp_enqueue_style('clitheme-style', get_stylesheet_uri());
    wp_enqueue_style('clitheme-signup-style', $cssurl . 'signup.css');

    $jsurl = get_template_directory_uri() . '/assets/js/';
    wp_enqueue_script('customjs', $jsurl . 'custom.js', array('jquery'), null, true);
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


add_action('init', function () {


    if (isset($_POST['signup'])) {


        $fname = sanitize_text_field($_POST['first_name']);
        $lname = sanitize_text_field($_POST['last_name']);
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
                echo "<p style='color:red;'>$error</p>";
            }
            return;
        }

        $userdata = array(
            'user_login' => $email,
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
        } else {
            echo "<script>alert('Registration failed: " . $user_id->get_error_message() . "');</script>";
        }
    }


  if (isset($_POST['signin'])) {
    $login    = sanitize_text_field($_POST['login']);
    $password = sanitize_text_field($_POST['password']);
    $remember = isset($_POST['remember']) ? true : false;

   
    if (is_email($login)) {
        $user = get_user_by('email', $login);
        if ($user) {
            $login = $user->user_login;
        }
    }

    $credentials = array(
        'user_login'    => $login,
        'user_password' => $password,
        'remember'      => $remember
    );

    $user = wp_signon($credentials, false);

    if (is_wp_error($user)) {
        echo '<div class="error-message">' . $user->get_error_message() . '</div>';
    } else {
        wp_redirect(home_url());
        exit;
    }
}


});