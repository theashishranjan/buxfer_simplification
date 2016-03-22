<!DOCTYPE html>
<html>
<head>
<style src="style.css"></style>
<script src="login.js"></script>

</head>
<body>

<div class="loginform-in">
<h1>Buxfer Login</h1>
<div class="err" id="add_err"></div>
<fieldset>
    <form action="./" method="post">
        <ul>
            <li> <label for="name">Email </label>
            <input type="text" size="30"  name="name" id="name"  /></li>
            <li> <label for="name">Password</label>
            <input type="password" size="30"  name="word" id="word"  /></li>
            <li> <label></label>
            <input type="submit" id="login" name="login" value="Login" class="loginbutton" ></li>
        </ul>
         </form>
</fieldset>
</div>

</body>
</html>