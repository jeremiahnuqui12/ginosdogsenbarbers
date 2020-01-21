<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title></title>
    <meta name="google-signin-client_id" content="744992195094-snkbqp91qobc0d5i8hqrhe50m7chtjhi.apps.googleusercontent.com">
	<script src="https://apis.google.com/js/platform.js" async defer></script>
  </head>
  <body>
    <h2>Google login</h2>
    <div class="g-signin2" data-onsuccess="onSignIn" data-theme="dark" style="display: none;"></div>
    <button type="button" onclick="gapi.auth2.getAuthInstance().signOut();">Google Sign out</button>
    <hr>
    <hr>
    <hr>
    <!-- In the callback, you would hide the gSignInWrapper element on a successful sign in -->

    <script src="https://apis.google.com/js/api:client.js?onload=onLoadCallback" async defer></script>
  </body>
</html>
