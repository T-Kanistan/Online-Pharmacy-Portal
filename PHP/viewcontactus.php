<?php session_start(); ?>
<?php
// Include database connection
require_once 'conn.php'; 

// Checking if a user is logged in
if (!isset($_SESSION['user_id'])) {
    header('location: loginForm.php');
    exit();
}

// Initialize contact us list
$contactusList = '';

// Getting the list of items
$query = "SELECT * FROM contactus ORDER BY cmtID DESC";
$uIdeas = mysqli_query($conn, $query);

if ($uIdeas) {
    // Loop through the contactus table and getting all the details as a table
    while ($uIdea = mysqli_fetch_assoc($uIdeas)) {
        $contactusList .= "<tr>";
        $contactusList .= "<td>{$uIdea['uname']}</td>";
        $contactusList .= "<td>{$uIdea['email']}</td>";
        $contactusList .= "<td>{$uIdea['mobileNo']}</td>";
        $contactusList .= "<td>{$uIdea['userIdeas']}</td>";
        $contactusList .= "<td>{$uIdea['comment-date-and-Time']}</td>";
    
        $contactusList .= "<td>
                            <form method='POST' action=''>
                                <input type='hidden' name='cmtID' value='{$uIdea['cmtID']}'>
                                <input class='contact-dltbtn' type='submit' name='delete' value='Delete' onclick='return confirm(\"Are you sure you want to delete this entry?\");'>
                            </form>
                          </td>";
        $contactusList .= "</tr>";
    }
} else {
    echo "Database query failed.";
}

// Handle delete request
if (isset($_POST['delete']) && isset($_POST['cmtID'])) {
    $cmtID = mysqli_real_escape_string($conn, $_POST['cmtID']);

    // Delete query
    $query = "DELETE FROM contactus WHERE cmtID='$cmtID'";
    $result = mysqli_query($conn, $query);

    if ($result) {
        // Redirect back with a success message
        header('location: viewContactUs.php?message=Entry deleted successfully.');
        exit();
    } else {
        // Redirect back with an error message
        header('location: viewContactUs.php?message=Error deleting entry.');
        exit();
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
    <link rel="icon" href="../Images/medimart_logo.png">
    <link rel="stylesheet" href="../CSS/template.css">
    <link rel="stylesheet" href="../CSS/viewcontactus.css">
    
    <script src="../JS/admindashboard.js"></script>
</head>

<body>
    <div class="nav-box">
        <div class="header">
            <a href="#default" class="logo"><img class="mmlogo" src="../Images/medimart_logo.png" alt="MediMart Logo"></a>
            <div class="header-right">
                <a href="#" onclick="adminDashBoard();"><img src="../Images/profile_icon.png" style='width: 40px; height: 40px;' ><br>&nbsp;&nbsp;&nbsp;<?php echo $_SESSION['name']; ?></a>
            </div>
        </div>
        <div class="menu">
            <div class="menu-links">
                <a class="active" onclick="adminDashBoard();">Home</a>
                <a href="#" onclick="addnewItem();">Add Items</a>
                <a href="#" onclick="viewItems();">View Items, Update & Delete</a>
                <a href="#" onclick="viewContactUs();">Contact Us</a>
                <a href="#" onclick="viewPreupOrders();">Prescription Orders</a>
                <a href="#" onclick="viewCartOrders();">Cart Orders</a>
            </div>
            <div class="search-container"></div>
        </div>
    </div>

    <main class="contactusList-main">
        <h2>User Ideas and Inquiries List</h2>
        <br/>
        
        <!-- Display success or error message -->
        <?php if (isset($_GET['message'])): ?>
            <div class="message">
                <?php echo htmlspecialchars($_GET['message']); ?>
            </div>
        <?php endif; ?>

        <table class="contactusList">
            <tr>
                <th>Name</th>
                <th>E-mail</th>
                <th>Mobile No</th>
                <th>User Ideas</th>
                <th>Date and Time</th>
                <th>Action</th> <!-- New header for action -->
            </tr>
            <!-- Printing the table -->
            <?php echo $contactusList; ?>
        </table>
    </main>

    <footer>
        <div class="row primary">
            <div class="column about">
                <h3><img src="../Images/medimart_logo.png" style="width:250; height:80px" alt="MediMart Logo"></h3>
                <p>
                    We are committed to your health at Medi Mart. We now offer an
                    online shopping and ordering experience to make health and wellness
                    products more accessible to you. You may browse thousands of home
                    health and over-the-counter goods on our site.
                </p>
                <div class="social">
                    <a href="#"><img src="../Images/fb.png" style="width:40px; height:40px;" id="i1" onclick="fbLogin();"></a>
                    <a href="#"><img src="../Images/ig.png" style="width:40px; height:40px;" id="i2" onclick="instaLogin();"></a>
                    <a href="#"><img src="../Images/x.png" style="width:40px; height:40px;" id="i3" onclick="twitterLogin();"></a>
                    <a href="#"><img src="../Images/yt.png" style="width:40px; height:40px;" id="i4" onclick="youtube();"></a>
                    <a href="#"><img src="../Images/wa.png" style="width:40px; height:40px;" id="i5" onclick="whatsapp();"></a>
                </div>
            </div>
            <div class="column links"></div>
            <div class="column subscribe"></div>
        </div>
        <div class="row copyright">
            <p>© 2024 MadiMart.inc. All rights reserved.</p>
        </div>
    </footer>
</body>

</html>
