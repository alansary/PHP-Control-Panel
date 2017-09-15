<?php
    $msgClass = '';

    /**************************************************************************
    * IF SUBMIT
    **************************************************************************/

    if (filter_has_var(INPUT_POST, 'submit')) {

        /**************************************************************************
        * INPUT CHECK
        **************************************************************************/

        if (isset($_POST['name']))
            $name = htmlspecialchars($_POST['name']);
        if (isset($_POST['email']))
            $email = htmlspecialchars($_POST['email']);
        if (isset($_POST['message']))
            $message = htmlspecialchars($_POST['message']);
        if (isset($_POST['subject']))
            $subject = htmlspecialchars($_POST['subject']);

        /**************************************************************************
        * IF INPUT EXISTS
        **************************************************************************/

        if (!empty($name) && !empty($email) && !empty($message) && !empty($subject)) {
            // Check Email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['message'] = 'Please use a valid email';
                $msgClass= 'alert-danger';
            }

            /**************************************************************************
            * EMAIL IS VALID
            **************************************************************************/

            else {
                $toEmail = 'mohamed_alansary@rocketmail.com';
                $subject = $subject;
                $body = '<h2>Contact Request</h2>
                        <h4>Name</h4><p>'.$name.'</p>
                        <h4>Email</h4><p>'.$email.'</p>
                        <h4>Message</h4><p>'.$message.'</p>';

                // Email Headers
                $headers = "MIME-Version: 1.0"."\r\n";
                $headers .= "Content-Type:text/html;charset=UTF-8"."\r\n";

                // Additional Headers
                $headers .= "From: ".$name."<".$email.">"."\r\n";

                /**************************************************************************
                * EMAIL HAS BEEN SENT SUCCESSFULLY
                **************************************************************************/

                if (mail($toEmail, $subject, $body, $headers)) {
                    $_SESSION['message'] = 'Your email has been sent, we will get back to you soon';
                    $msgClass = 'alert-success';
                }

                /**************************************************************************
                * ERROR SENDING THE EMAIL
                **************************************************************************/

                else {
                    $_SESSION['message'] = 'ERROR: Your email has not been sent, please try again later';
                    $msgClass = 'alert-danger';
                }
            }
        }

        /**************************************************************************
        * IF INPUT IS MISSING
        **************************************************************************/

        else {
            $_SESSION['message'] = 'Please fill in all fields';
            $msgClass = 'alert-danger';
        }
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Contact Us</title>
    <link rel="stylesheet" href="layout/css/bootstrap.min.css"/>
</head>
<body>
    <nav class="navbar navbar-default">
    <div class="container">
            <div class="navbar-header">
        <a class="navbar-brand" href="<?php echo $_SERVER['PHP_SELF']; ?>">Contact Us</a>
            </div>
    </div>
    </nav>
    <div class="container">
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert <?php echo $msgClass; ?>">
        <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
            </div>
    <?php endif; ?>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <div class="form-group">
        <label>Name</label>
        <input type="text" name="name" class="form-control" value="<?php echo isset($_POST['name']) ? $name : ''; ?>" required="required" autocomplete="off">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="text" name="email" class="form-control" value="<?php echo isset($_POST['email']) ? $email : ''; ?>" required="required" autocomplete="off">
            </div>
            <div class='form-group'>
                <label>Subject</label>
                <input type='text' name="subject" class="form-control" value="<?php echo isset($_POST['subject']) ? $subject : ''; ?>" required="required" autocomplete="off">
            </div>
            <div class="form-group">
        <label>Message</label>
        <textarea name="message" class="form-control" required="required" autocomplete="off"><?php echo isset($_POST['message']) ? $message : ''; ?></textarea>
            </div>
            <br/>
            <button type="submit" name="submit" class="btn btn-primary">Submit</button>
    </form>
    </div>
</body>
</html>