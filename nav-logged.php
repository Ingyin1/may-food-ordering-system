<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db_connection.php';

if (!isset($_SESSION['userloggedin']) || !$_SESSION['userloggedin']) {
    header('Location: login.php');
    exit;
}

$useremail = $_SESSION['email'] ?? '';

function get_UserInfo($email) {
    global $conn;
    $stmt = $conn->prepare("SELECT profile_image, firstName FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return [
        'profile_image' => $row['profile_image'] ?? 'default.jpg',
        'name' => $row['firstName'] ?? 'User'
    ];
}

$userinfo = get_UserInfo($useremail);

$categories = [];
$sql = "SELECT catId, catName FROM menucategory ORDER BY catName ASC";
$cat_result = $conn->query($sql);
if ($cat_result) {
    while ($row = $cat_result->fetch_assoc()) {
        $categories[] = $row;
    }
}

$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;500;600&family=Chewy&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --primary-food: #FF714D; 
            --dark-navy: #2C3E50;
            --fresh-green: #2ECC71;
            --white: #FFFFFF;
            --shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        }

        body { font-family: 'Poppins', sans-serif; }

        .navbar {
            background-color: var(--white) !important;
            padding: 12px 0;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            border-bottom: 2px solid #f8f9fa;
        }

        .navbar-brand {
            font-family: 'Chewy', cursive;
            font-size: 30px;
            color: var(--primary-food) !important;
            margin-left: 5%;
        }

        .nav-link {
            color: var(--dark-navy) !important;
            font-family: 'Lexend', sans-serif;
            font-weight: 500;
            padding: 8px 18px !important;
            transition: 0.3s;
            border-radius: 30px;
        }

        .nav-link:hover {
            color: var(--primary-food) !important;
            background: #fff5f2;
        }

        .nav-link.active {
            background-color: var(--primary-food) !important;
            color: white !important;
        }

        .dropdown-menu {
            border: none;
            border-radius: 12px;
            box-shadow: var(--shadow);
            padding: 10px;
            margin-top: 10px !important;
        }

        .dropdown-item {
            border-radius: 8px;
            padding: 10px 20px;
            transition: 0.2s;
        }

        .dropdown-item:hover {
            background-color: var(--primary-food);
            color: white;
            padding-left: 25px;
        }

        .nav-profile {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--primary-food);
            padding: 2px;
        }

        .cart-icon-wrapper {
            position: relative;
            font-size: 1.4rem;
            color: var(--fresh-green);
            margin-right: 20px;
            transition: 0.3s;
        }
        
        .cart-icon-wrapper:hover { transform: scale(1.1); color: var(--primary-food); }

        #cart-item {
            position: absolute;
            top: -5px;
            right: -10px;
            background: var(--primary-food);
            font-size: 10px;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">May Food</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar">
                <span class="fa-solid fa-bars-staggered"></span>
            </button>

            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title font-chewy" style="color: var(--primary-food)">May Food</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
                </div>
                <div class="offcanvas-body">
                    <ul class="navbar-nav justify-content-center flex-grow-1">
                        <li class="nav-item">
                            <a class="nav-link <?= ($current_page == 'index.php') ? 'active' : '' ?>" href="index.php">Home</a>
                        </li>
                        
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                              Menu
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="menu.php">All Menu</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <?php foreach ($categories as $cat): ?>
                                    <li><a class="dropdown-item" href="menu.php?catId=<?= $cat['catId'] ?>"><?= htmlspecialchars($cat['catName']) ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </li>

                        <li class="nav-item"><a class="nav-link" href="index.php#Reservation">Reservation</a></li>
                        <li class="nav-item"><a class="nav-link" href="index.php#About-Us">About Us</a></li>
                        <li class="nav-item"><a class="nav-link <?= ($current_page == 'qa.php') ? 'active' : '' ?>" href="qa.php">Help</a></li>
                    </ul>

                    <div class="d-flex align-items-center nav-actions pe-lg-5">
                        <a href="cart.php" class="cart-icon-wrapper">
                            <i class="fas fa-shopping-basket"></i>
                            <span id="cart-item" class="badge rounded-pill">0</span>
                        </a>

                        <div class="dropdown">
                            <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown" style="text-decoration: none;">
                                <img src="uploads/<?= htmlspecialchars($userinfo['profile_image']) ?>" alt="Profile" class="nav-profile">
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li class="px-3 py-2 small fw-bold text-muted">Hi, <?= htmlspecialchars($userinfo['name']) ?>!</li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user-circle me-2"></i> Profile</a></li>
                                <li><a class="dropdown-item" href="orders.php"><i class="fas fa-receipt me-2"></i> My Orders</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</body>
</html>