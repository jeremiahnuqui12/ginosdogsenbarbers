<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta name="google-signin-client_id" content="744992195094-snkbqp91qobc0d5i8hqrhe50m7chtjhi.apps.googleusercontent.com">
    <title></title>
    <script src="https://apis.google.com/js/platform.js" async defer></script>
    <script type="text/javascript">
    </script>
  </head>
  <body>
    <div class="g-signin2" data-onsuccess="onSignIn"></div>
    <script type="text/javascript">
      function onSignIn(googleUser) {
        var profile = googleUser.getBasicProfile();
        console.log('ID: ' + profile.getId()); // Do not send to your backend! Use an ID token instead.
        console.log('Name: ' + profile.getName());
        console.log('Image URL: ' + profile.getImageUrl());
        console.log('Email: ' + profile.getEmail()); // This is null if the 'email' scope is not present.
      }
    </script>
  </body>
</html>
