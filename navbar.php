<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db_connection.php';
$isLoggedIn = isset($_SESSION['userloggedin']) && $_SESSION['userloggedin'];
$useremail = $_SESSION['email'] ?? '';

function get_UserInfo($email) {
    global $conn;
    if (empty($email)) return null;
    $stmt = $conn->prepare("SELECT profile_image, firstName FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

$userinfo = ($isLoggedIn) ? get_UserInfo($useremail) : null;
$categories = [];
$sql = "SELECT catId, catName FROM menucategory ORDER BY catName ASC";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
}

$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>May Food Navbar</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@300;500;700&family=Chewy&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

    <style>
        :root {
            --brand-blue: #4DA8FF;
            --brand-orange: #FF714D;
            --dark-text: #2c3e50;
        }

        body { font-family: "Poppins", sans-serif; }

        .navbar {
            background-color: var(--brand-blue);
            border-bottom-left-radius: 40px;
            border-bottom-right-radius: 40px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            padding: 10px 0;
            transition: all 0.3s;
        }

        .navbar-brand {
            font-family: "Chewy", cursive;
            font-size: 28px;
            color: white !important;
            margin-left: 20px;
        }

        .nav-link {
            color: rgba(255,255,255,0.9) !important;
            font-family: "Lexend", sans-serif;
            font-weight: 500;
            padding: 8px 15px !important;
        }

        .nav-link:hover, .nav-link.active {
            color: white !important;
            font-weight: 700;
        }
        .nav-profile-img {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid white;
        }
        .Btn {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            width: 40px;
            height: 40px;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition-duration: 0.3s;
            background: white;
            box-shadow: 2px 2px 10px rgba(0,0,0,0.1);
        }

        .sign { width: 100%; display: flex; align-items: center; justify-content: center; transition-duration: 0.3s; }
        .sign svg { width: 18px; fill: var(--brand-blue); }
        .text { position: absolute; right: 0%; width: 0%; opacity: 0; color: var(--brand-blue); font-weight: 600; transition-duration: 0.3s; }

        .Btn:hover { width: 110px; border-radius: 40px; }
        .Btn:hover .sign { width: 30%; padding-left: 10px; }
        .Btn:hover .text { opacity: 1; width: 70%; padding-right: 10px; }
        .cart-wrapper { position: relative; color: white; font-size: 22px; margin-right: 15px; }
        #cart-item { position: absolute; top: -5px; right: -10px; background: var(--brand-orange); font-size: 10px; }

        .dropdown-menu { border: none; box-shadow: 0 5px 15px rgba(0,0,0,0.1); border-radius: 12px; }

        @media (max-width: 768px) {
            .navbar { border-radius: 0; }
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-md fixed-top">
        <div class="container-fluid px-lg-5">
            <a class="navbar-brand" href="index.php">May Food</a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar">
                <span class="fas fa-bars text-white"></span>
            </button>

            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar">
                <div class="offcanvas-header" style="background: var(--brand-blue); border-bottom: 1px solid rgba(255,255,255,0.2);">
                    <h5 class="offcanvas-title text-white font-chewy">Flavour Fiesta</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
                </div>
                
                <div class="offcanvas-body" style="background: var(--brand-blue);">
                    <ul class="navbar-nav justify-content-center flex-grow-1">
                        <li class="nav-item">
                            <a class="nav-link <?= ($current_page == 'index.php') ? 'active' : '' ?>" href="index.php">Home</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Menu</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="menu.php">All Menu</a></li>
                                <?php foreach ($categories as $cat): ?>
                                    <li><a class="dropdown-item" href="menu.php?catId=<?= $cat['catId'] ?>"><?= htmlspecialchars($cat['catName']) ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                        <li class="nav-item"><a class="nav-link" href="index.php#Reservation">Reservation</a></li>
                        <li class="nav-item"><a class="nav-link" href="index.php#About-Us">About Us</a></li>
                        <li class="nav-item"><a class="nav-link <?= ($current_page == 'qa.php') ? 'active' : '' ?>" href="qa.php">Q&A</a></li>
                    </ul>

                    <div class="d-flex align-items-center mt-3 mt-md-0">
                        <a href="cart.php" class="cart-wrapper">
                            <i class="fas fa-shopping-cart"></i>
                            <span id="cart-item" class="badge rounded-pill">0</span>
                        </a>

                        <?php if ($isLoggedIn): ?>
                            <div class="dropdown ms-3">
                                <a href="#" class="dropdown-toggle d-flex align-items-center text-decoration-none" data-bs-toggle="dropdown">
                                    <img src="uploads/<?= htmlspecialchars($userinfo['profile_image'] ?? 'default.jpg') ?>" class="nav-profile-img">
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li class="px-3 py-1 small fw-bold text-muted">Hi, <?= htmlspecialchars($userinfo['firstName'] ?? 'User') ?></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user-circle me-2"></i> Profile</a></li>
                                    <li><a class="dropdown-item" href="orders.php"><i class="fas fa-receipt me-2"></i> Orders</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                                </ul>
                            </div>
                        <?php else: ?>
                            <a href="login.php" class="text-decoration-none ms-3">
                                <button class="Btn">
                                    <div class="sign">
                                        <svg viewBox="0 0 512 512"><path d="M217.9 105.9L340.7 228.7c7.2 7.2 11.3 17.1 11.3 27.3s-4.1 20.1-11.3 27.3L217.9 406.1c-6.4 6.4-15 9.9-24 9.9c-18.7 0-33.9-15.2-33.9-33.9l0-62.1L32 320c-17.7 0-32-14.3-32-32l0-64c0-17.7 14.3-32 32-32l128 0 0-62.1c0-18.7 15.2-33.9 33.9-33.9c9 0 17.6 3.6 24 9.9zM352 416l64 0c17.7 0 32-14.3 32-32l0-256c0-17.7-14.3-32-32-32l-64 0c-17.7 0-32-14.3-32-32s14.3-32 32-32l64 0c53 0 96 43 96 96l0 256c0 53-43 96-96 96l-64 0c-17.7 0-32-14.3-32-32s14.3-32 32-32z"></path></svg>
                                    </div>
                                    <div class="text">LOGIN</div>
                                </button>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>