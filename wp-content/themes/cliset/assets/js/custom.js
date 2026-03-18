jQuery(document).ready(function ($) {
  $("#togglePassword").on("click", function () {
    console.log("Password toggle clicked");

    const input = $("#password");
    const type = input.attr("type") === "password" ? "text" : "password";
    input.attr("type", type);

    $(this).text(type === "password" ? "Show" : "Hide");
  });

  $("#toggleConfirmPassword").on("click", function () {
    console.log("Confirm password toggle clicked");

    const input = $("#confirmPassword");
    const type = input.attr("type") === "password" ? "text" : "password";
    input.attr("type", type);

    $(this).text(type === "password" ? "Show" : "Hide");
  });
});
