<?php session_start(); ?>
<?php
//include database connection
require_once 'conn.php'; ?>
<?php
$itemList = '';
$items = '';
$errors = array();
//check if there is a search term
if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $query = "SELECT * FROM item WHERE (genericName LIKE '%{$search}%' OR brandName LIKE '%{$search}%') AND isDeleted = 0 ORDER BY genericName";

    $items = mysqli_query($conn, $query);
    if ($items) {
        //loop through the database and find a match
        while ($item = mysqli_fetch_assoc($items)) {
            $itemList .= "<a href=\"searchedItem.php?item_ID={$item['itemID']}\">{$item['genericName']} / {$item['brandName']}</a>";
        }
    } else {
        //if there is an error
        $errors[] = 'Database query failed.';
    }
}
?>
<?php
//check for form submission
if (isset($_POST['submit'])) {

    $errors = array();
    //check if the username and password has been entered

    if (!isset($_POST['uname']) || strlen(trim($_POST['uname'])) < 1) {
        $errors[] = 'Username is missing / invalid';
    }
    if (!isset($_POST['psw']) || strlen(trim($_POST['psw'])) < 1) {
        $errors[] = 'Password is missing / invalid';
    }
    //check if there are any errors in the form
    if (empty($errors)) {
        //save username and password into variables
        $username = mysqli_real_escape_string($conn, $_POST['uname']);
        $password = mysqli_real_escape_string($conn, $_POST['psw']);

        //prepare database query without encryption
        $query = "SELECT * FROM mmuser WHERE eMailAddress = '{$username}' AND UPW = '{$password}' LIMIT 1";

        $result_set = mysqli_query($conn, $query);

        if (mysqli_num_rows($result_set) > 0) {
            $row = mysqli_fetch_array($result_set);

            if ($row['mmRole'] == 'User') {
                //saving user data into session variables
                $_SESSION['user_id'] = $row['mmUID'];
                $_SESSION['name'] = $row['uName'];
                $_SESSION['email'] = $row['eMailAddress'];
                $_SESSION['mobileNo'] = $row['uMobileNo'];
                $_SESSION['address'] = $row['uAddress'];

                header('location: home.php');
            } else if ($row['mmRole'] == 'Admin') {
                //saving Admin data into session variables
                $_SESSION['user_id'] = $row['mmUID'];
                $_SESSION['name'] = $row['uName'];
                $_SESSION['email'] = $row['eMailAddress'];
                $_SESSION['mobileNo'] = $row['uMobileNo'];
                $_SESSION['address'] = $row['uAddress'];

                header('location: admindashboard.php');
            }
        } else {
            $errors[] = "Invalid username or password";
        }
    }
}
?>

<?php
//check for registration submission
if (isset($_POST['register'])) {

    $errors = array();
    //check if registration fields are filled
    if (!isset($_POST['uname']) || strlen(trim($_POST['uname'])) < 1) {
        $errors[] = 'Username is missing / invalid';
    }
    if (!isset($_POST['psw']) || strlen(trim($_POST['psw'])) < 1) {
        $errors[] = 'Password is missing / invalid';
    }

    //check if there are any errors in the form
    if (empty($errors)) {
        //save registration details
        $username = mysqli_real_escape_string($conn, $_POST['uname']);
        $password = mysqli_real_escape_string($conn, $_POST['psw']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);
        $address = mysqli_real_escape_string($conn, $_POST['address']);

        //assign 'User' role by default
        $role = 'User';

        //prepare insert query to add a new user
        $query = "INSERT INTO mmuser (uName, eMailAddress, UPW, uMobileNo, uAddress, mmRole) 
                  VALUES ('{$username}', '{$email}', '{$password}', '{$mobile}', '{$address}', '{$role}')";

        $result_set = mysqli_query($conn, $query);

        if ($result_set) {
            //automatically log in the newly registered user
            $_SESSION['user_id'] = mysqli_insert_id($conn);
            $_SESSION['name'] = $username;
            $_SESSION['email'] = $email;
            $_SESSION['mobileNo'] = $mobile;
            $_SESSION['address'] = $address;

            header('location: home.php');
        } else {
            $errors[] = "Registration failed";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Medi Mart</title>
    <link rel="icon" href="../Images/medimart_logo.png"/>
    <link rel="stylesheet" href="../CSS/template.css"/>
    <link rel="stylesheet" href="../CSS/loginForm.css"/>
    
    <script src="../JS/home.js"></script>
    <script src="../JS/cancel.js"></script>
</head>

<body>
<div class="nav-box">
<div class="header">
    <a onclick="home();"><img class="mmlogo" src="../Images/medimart_logo.png" alt="MediMart Logo"></a>
    <div class="header-right">
        <?php
        //if there is a user display username
        if (isset($_SESSION['user_id'])) {
                echo "<a onclick=\"myacc();\"><img src='../Images/profile_icon.png' alt='Profile Icon' style='width: 40px; height: 40px;'><br>&nbsp;&nbsp;&nbsp;";
                echo $_SESSION['name'] . "</a>";
            } else {
                //if the user is not a registered user display a register button
                echo "<a onclick=\"register();\"><img src=\"../Images/profile_icon.png\" alt=\"Profile Icon\" style=\"width: 40px; height: 40px;\"><br>Sign in</a>";
            }
        ?>
        <?php
        //display the shopping cart button if there is at least one item added to cart
        if (!empty($_SESSION["shopping_cart"])) {
            $cart_count = count(array_keys($_SESSION["shopping_cart"]));
            ?>
            <a href="cart.php"><img src="../Images/cart_icon.png" style="width:25px; height:25px;"> : <?php echo $cart_count; ?></a>
            <?php
        }
        ?>
    </div>
</div>
<div class="menu">
    <div class="menu-links">
        <a class="active" href="#" onclick="home();">Home</a>
        <a href="#" onclick="medicine();">Medicines</a>
        <a href="#" onclick="medicalDevices();">Medical Devices</a>
        <a href="#" onclick="traditionalRemedies();">Traditional Remedies</a>
        <a href="#" onclick="aboutUs();">About us</a>
    </div>
    <!--showing the results of search-->
    <div class="search-container">
        <form action="loginForm.php" method="GET">
            <input type="text" placeholder="Search.." name="search"/>
            
        </form>
        <div class="dropdown-content" id="drop">
            <?php
            if ($items) {
                echo $itemList;
            }
            ?>
        </div>
    </div>
</div>
</div>

<div class="mother-left-right">
    <div class="child-left">
        <div class="LoginPage">
            <form action="loginForm.php" method="post">
                <div class="login-container">
                    <h1>Login</h1>
                    <p>Already a member? Please login</p>
                    <hr/>
                    <?php
                    if (isset($errors) && !empty($errors)) {
                        echo '<p class="error">Invalid Username or password</p>';
                    }
                    ?>
                    <label for="uname"><b>Username</b></label>
                    <input type="text" placeholder="Enter Your E-mail as Username" name="uname" required/>

                    <label for="psw"><b>Password</b></label>
                    <input type="password" placeholder="Enter Password" name="psw" required/>

                    <div class="not-member"><a href="#" onclick="notAmember();">Not a member? Register here</a>
                    </div>
                    <button type="submit" name="submit">Login</button>
                </div>

                <div class="pw-container">
                    <button type="button" class="cancelbtn" onclick="cancelLogin();">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    <div class="child-right"><img src="../Images/aboutus.svg"></div>
</div>

<footer>
    <div class="row primary">
        <div class="column about">
        <h3><img src="../Images/medimart_logo.png" style="width:250; height:80px" alt="MediMart Logo"></h3>
        <p>
            At Medi Mart, your health is our top priority. 
            We've introduced an online shopping and ordering experience to make accessing health and wellness products easier than ever. 
            Browse through thousands of home healthcare and over-the-counter products right from our site,
            and enjoy the convenience of having them delivered to your door.

        </p>
            <div class="social">
                <a href="#"><img src="../Images/fb.png" style="width:40px; height:40px;" id="i1" onclick="fbLogin();"></a>
                <a href="#"><img src="../Images/ig.png" style="width:40px; height:40px;" id="i2" onclick="instaLogin();"></a>
                <a href="#"><img src="../Images/x.png" style="width:40px; height:40px;" id="i3" onclick="twitterLogin();"></a>
                <a href="#"><img src="../Images/yt.png" style="width:40px; height:40px;" id="i4" onclick="youtube();"></a>
                <a href="#"><img src="../Images/wa.png" style="width:40px; height:40px;" id="i5" onclick="whatsapp();"></a>
            </div>
        </div>
        <div class="column links">
            <h3>Customer Service</h3>
            <ul>
                <li>
                    <a href="#" onclick="contactUs();">Contact Us</a>
                </li>
                <li>
                    <a href="#" onclick="privacyPolicy();">Privacy Policy</a>
                </li>
                <li>
                    <a href="#" onclick="aboutUs();">About Us</a>
                </li>
            </ul>
        </div>
        <div class="column subscribe">
            <h3>Newsletter</h3>
            <div class="footersearch">
                <input type="email" placeholder="Your email id here"/>
                <button>Subscribe</button>
            </div>
        </div>
    </div>
    <div class="row copyright">
        <p>© 2024 Medi Mart - All rights reserved || Designed By Joel Nithushan</p>
    </div>
</footer>
</body>
</html>
