<?php
require_once('Connect_class.php');
$db = new db_connect();
if (isset($_GET['temp_process']) && !empty($_GET['temp_process'])) {
    if (isset($_POST['reset'])) {
        if ($_POST['new_password'] == $_POST['confirm_password']) {
            $data = array(
                'table' => 'users',
                'data' => " temp_process='',password='" . sha1($_POST['new_password']) . "' ",
                'where' => " temp_process='" . $_GET['temp_process'] . "' "
            );
            $update = $db->update($data);
            if ($update['affected_rows'] > 0) {
                $error = '<div class="alert alert-success" role="alert">Alert ! Password changed successfully</div>';
            } else {
                $error = '<div class="alert alert-danger" role="alert">Alert ! Token expired</div>';
            }
        } else {
            $error = '<div class="alert alert-danger" role="alert">Alert ! Password not matched</div>';
        }
    }
} else {
    echo 'page_expired';
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Viaspot (Reset password)</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous"></script>
        <script src="validate.js"></script>
        <script>
            $(function () {
                $.validator.addMethod("Solidpass", function (value, element) {
                    return this.optional(element) || /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}$/i.test(value);
                }, "Password are 6 characters with uppercase letters, lowercase letters and at least one number.");
                $('#form-reset-password').validate({
                    debug: true,
                    errorClass: 'is-invalid',
                    validClass: 'is-valid',
                    errorElement: 'div',
                    highlight: function (element, errorClass, validClass) {
                        $(element).closest(".form-control").addClass("is-invalid");
                    },
                    unhighlight: function (element, errorClass, validClass) {
                        $(element).closest(".is-invalid").removeClass("is-invalid");
                    },
                    rules: {
                        new_password: {
                            required: true,
                            minlength: 6,
                            Solidpass: "Password must solid"
                        },
                        confirm_password: {
                            required: true,
                            equalTo: "#new_password"
                        },
                        messages: {
                            new_password: {
                                required: "Please enter an password",
                                minlength: "Minimum lenght must be 6 or greater!"
                            },
                            confirm_password: {
                                required: "Please enter your password again",
                                equalTo: "Password does not match"
                            }
                        }
                    },
                    submitHandler: function (form) {
                        form.submit();
                    }
                });
            });
        </script>
    </head>
    <body>        
        <div class="container">
            <div class="row">
                <div class="col-md-12 col-12 col-sm-12 p-5">
                    <div class="card">
                        <?php
                        echo @$error;
                        ?>
                        <div class="card-body">
                            <h4 class="card-title text-center">Reset Your Password</h4>                            
                            <form method="post" id="form-reset-password">
                                <div class="form-group">
                                    <input type="password" class="form-control" name="new_password" id="new_password" placeholder="New Password">
                                    <input type="hidden" name="temp_process" value="<?= $_GET['temp_process'] ?>">
                                </div>
                                <div class="form-group ">
                                    <input type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder="Confirm password">                                        
                                </div>
                                <center>
                                    <input type="submit" class="btn btn-primary btn-lg" name="reset" value="submit">

                                </center>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
