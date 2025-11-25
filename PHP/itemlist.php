<?php session_start(); ?>
<?php
//include database connection
require_once 'conn.php'; ?>
<?php
//checking if a user is logged in
if (!isset($_SESSION['user_id'])) {
    header('location: loginForm.php');
}
$itemList = '';

//check if there is a search term
if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $query = "SELECT * FROM item WHERE (genericName LIKE '%{$search}%' OR brandName LIKE '%{$search}%' OR itemPrice LIKE '%{$search}%') AND isDeleted = 0 ORDER BY genericName";
} else {
    //getting the list of items
    $query = "SELECT * FROM item WHERE isDeleted = 0 ORDER BY genericName";
}

$items = mysqli_query($conn, $query);

if ($items) {
    //loop through the item table and getting all the details as a table
    while ($item = mysqli_fetch_assoc($items)) {
        $itemList .= "<tr>";
        $itemList .= "<td>{$item['genericName']}</td>";
        $itemList .= "<td>{$item['brandName']}</td>";
        $itemList .= "<td>{$item['itemPrice']}</td>";
        $itemList .= "<td>{$item['type']}</td>";
        $itemList .= "<td><button class=\"itemListEditBtn\"><a href=\"modifyItem.php?item_ID={$item['itemID']}\">Edit</a></button></td>";
        $itemList .= "<td><button class=\"itemListDltBtn\"><a href=\"deleteItem.php?item_ID={$item['itemID']}\" onclick=\"return confirm('Are you sure you want to delete this item ?');\">Delete</a></button></td>";
        $itemList .= "</tr>";
    }
} else {
    echo "Database query failed.";
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
    <link rel="stylesheet" href="../CSS/itemlist.css"/>
    
    <script src="../JS/admindashboard.js"></script>
</head>

<body>
<div class="nav-box">
<div class="header">
    <a href="#default" class="logo"><img class="mmlogo" src="../Images/medimart_logo.png" class="far fa-eye"></a>
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
    <div class="search-container">

    </div>
</div>
</div>
<main class="itemList-main">
    <h2>Item list</h2>

    <div class="search">
        <form action="itemlist.php" method="GET">
            <p><input type="text" name="search" id="searchbox" placeholder="search here....and press enter" autofocus
                      required></p>
        </form>
    </div>

    <table class="itemList">
        <tr>
            <th>Generic Name</th>
            <th>Brand Name</th>
            <th>Price Rs.</th>
            <th>Type</th>
            <th>Edit</th>
            <th>Delete</th>
        </tr>
        <!--printing the table-->
        <?php echo $itemList; ?>
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
        <div class="column links">

        </div>
        <div class="column subscribe">
        </div>
    </div>
    <div class="row copyright">
        <p>© 2024 MadiMart.inc. All rights reserved.</p>
    </div>
</footer>
</body>

</html>