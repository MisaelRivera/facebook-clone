<?php
    require_once('core/load.php');
    require_once('connect/DB.php');

    if (isset($_POST['first_name']) && !empty($_POST['first_name']))
    {
        $firstName = $_POST['first_name'];
    }

    if (isset($_POST['last_name']) && !empty($_POST['last_name']))
    {
        $lastName = $_POST['last_name'];
    }

    if (isset($_POST['email_mobile']) && !empty($_POST['email_mobile']))
    {
        $emailMobile = $_POST['email_mobile'];
    }

    if (isset($_POST['password']) && !empty($_POST['password']))
    {
        $password = $_POST['password'];
    }

    if (isset($_POST['birth_day']) && !empty($_POST['birth_day']))
    {
        $birthDay = $_POST['birth_day'];
    }

    if (isset($_POST['birth_month']) && !empty($_POST['birth_month']))
    {
        $birthMonth = $_POST['birth_month'];
    }

    if (isset($_POST['birth_year']) && !empty($_POST['birth_year']))
    {
        $birthYear = $_POST['birth_year'];
    }

    if (isset($birthDay) && isset($birthMonth) && isset($birthYear))
    {
        $birthDate =  "$birthYear-$birthMonth-$birthDay"; 
    }

    if (isset($_POST['gender']) && !empty($_POST['gender']))
    {
        $gender = $_POST['gender'];
    }

    if (!isset($firstName) || !isset($lastName) || !isset($emailMobile) || !isset($gender))
    {
        $error = "All field are required";
    } else {
        $firstName = $loadFromUser->sanitizeInput($firstName);
        $lastName = $loadFromUser->sanitizeInput($lastName);
        $emailMobile = $loadFromUser->sanitizeInput($emailMobile);
        $password = $loadFromUser->sanitizeInput($password);
        $birthDate = $loadFromUser->sanitizeInput($birthDate);
        $gender = $loadFromUser->sanitizeInput($gender);
        $screenName = $firstName . "_" . $lastName;

        $screenNameFound = DB::query('SELECT screen_name FROM users WHERE screen_name = :screen_name', [':screen_name' => $screenName]);
        if ($screenNameFound) {
            $screenNameRand = rand();
            $userLink = $screenName . '' . $screenNameRand;
        } else {
            $userLink = $screenName;
        }
        $emailValidation = preg_match("/^[_A-z0-9-]+[\._A-z0-9-]*@[a-z]+\.[a-z]+/", $emailMobile);
        $phoneValidation = preg_match("/[0-9]{10}/", $emailMobile);
        if (!$emailValidation && !$phoneValidation) {
            $error = 'Invalid email or mobile number format. Please try again!';
        } else {
            if (!filter_var($emailMobile)) {
                $error = "Invalid Email format";
            } else if (strlen($firstName) > 20) {
                $error = "Name must be between 2-20";
            } else if (strlen($password) < 5 || strlen($password) >= 60) {
                $error = "The password is either too short or either too long.";
            } else {
                if ((filter_var($emailMobile, FILTER_VALIDATE_EMAIL)) && $loadFromUser->isEmailUnique($emailMobile)) {
                    $error = "Email is already taken";
                } else {
                    $userId = $loadFromUser->create('users', [
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'email' => $emailMobile,
                        'password' => password_hash($password, PASSWORD_BCRYPT),
                        'screen_name' => $screenName,
                        'user_link' => $userLink,
                        'birthday' => $birthDate,
                        'gender' => $gender
                    ]);
                    $strong = true;
                    $token = bin2hex(openssl_random_pseudo_bytes(64, $strong));
                    $loadFromUser->create('tokens', [
                        'token' => $token,
                        'user_id' => $userId
                    ]);

                    setcookie('token', $token, time() + (60 * 60 * 24), '/', null, null, true);
                    header('Location: index.php');
                }
            }
        }
    }
    
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facebook</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header></header>
    <main>
        <div class="left-side">
            <img src="assets/image/sign-in.png" alt="">
        </div>
        <div class="right-side">
            <div class="error">
                <?= isset($error) ? $error:''?>
            </div>
            <h1>Create an account</h1>
            <p style="color:#212121; font-size:20px;">It's free and always will be</p>
            <form 
                action="sign.php" 
                class="form"
                method="post"
                name="user-sign-up">
                <div class="sign-up-form">
                    <div class="sign-up-name">
                        <input 
                            type="text" 
                            name="first_name"
                            class="text-field" 
                            id="first-name"
                            placeholder="First Name">
                        <input 
                            type="text" 
                            name="last_name"
                            class="text-field"
                            id="last-name" 
                            placeholder="Last Name">
                    </div>
                    <div class="sign-wrap-mobile">
                        <input 
                            type="text" 
                            name="email_mobile" 
                            class="text-input"
                            id="up-email"
                            placeholder="Mobile number or email">
                    </div>
                    <div class="sign-up-password">
                        <input 
                            type="password" 
                            name="password"
                            id="password"
                            class="text-input"
                            placeholder="Password">
                    </div>
                    <div class="sign-up-birthday">
                        <div class="bday">Birthday</div>
                        <div class="form-birthday">
                            <select 
                                name="birth_day" 
                                id="days"
                                class="select-body"></select>
                            <select 
                                name="birth_month" 
                                id="months"
                                class="select-body"></select>
                            <select 
                                name="birth_year" 
                                id="years"
                                class="select-body"></select>
                        </div>
                    </div>
                    <div class="gender-wrap">
                        <input 
                            type="radio" 
                            name="gender"
                            id="female"
                            class="m0" 
                            value="female">
                        <label for="female" class="gender">Female</label>
                        <input 
                            type="radio" 
                            name="gender"
                            class="m0"
                            id="male" 
                            value="male">
                        <label for="male" class="gender">Male</label>
                    </div>
                    <div class="term">
                        By clicking Sign Up, you agree to our terms, Data policy and Cookie policy. You may recieve SMS notifications from us and can opt out at any time.
                    </div>
                    <div class="sign-up">
                        <button class="sign-up">
                            Sign up
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </main>
    <script src="assets/js/jquery.js"></script>
    <script>
        for (let i =new Date().getFullYear(); i > 1900; i--) {
            console.log(i);
            $('#years').append($('<option/>').val(i).html(i));
        }

        for (let i = 1; i < 13; i++) {
            $('#months').append($('<option/>').val(i).html(i));
        }

        updateNumberOfDays();
        
        function daysInMonth (year, month) {
            return new Date(year, month, 0).getDate();
        }

        function updateNumberOfDays ()
        {
            $('#days').html('');
            let year = $('#years').val(),
                month = $('#months').val();
            let days = daysInMonth(year, month);
            for (let i = 1; i <= days; i++) {
                $('#days').append($('<option/>').html(i).val(i));
            }
        }

        $('#months').on('change', updateNumberOfDays);

    </script>
</body>
</html>