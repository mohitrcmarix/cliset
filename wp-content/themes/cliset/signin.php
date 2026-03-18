<?php
/**
 * The template for displaying the Sign in page
 * template name: Sign In
 */
get_header();
?>
<div class="form_wrapper">
  <div class="form_container">
    <div class="title_container">
      <h2>Sign In</h2>
    </div>
    <div class="row clearfix">
      <form method="post" action="" autocomplete="off">
        <div class="input_field">
          <input type="text" name="login" placeholder="Username or Email" autocomplete="off" required />
        </div>
        <div class="input_field">
          <input type="password" name="password" placeholder="Password" autocomplete="new-password" id="password" required />
          <button type="button" id="togglePassword">Show</button>
        </div>
        <div class="input_field checkbox_option">
          <input type="checkbox" name="remember" id="remember">
          <label for="remember">Remember Me</label>
        </div>
        <input class="button" type="submit" name="signin" value="Sign In" />
        <p style="text-align:center; margin-top:15px;">
          <a href="#">Forgot Password?</a> |
          <a href="<?php echo esc_url(home_url('/sign-up/')); ?>">Create Account</a>
        </p>
      </form>
    </div>
  </div>
</div>

<?php
get_footer();
?>