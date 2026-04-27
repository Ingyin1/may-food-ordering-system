<?php
session_start();
include 'db_connection.php';
$categoryQuery = 'SELECT mc.catId, mc.catName 
                  FROM menucategory mc 
                  INNER JOIN menuitem mi ON mc.catId = mi.catId 
                  GROUP BY mc.catId';
$categoryResult = $conn->query($categoryQuery);

$categories = [];
if ($categoryResult) {
    while ($row = $categoryResult->fetch_assoc()) {
        $categories[] = $row; 
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Menu | May Food</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="menu.css?v=1.4">
    
    <style>
        .search-container { max-width: 600px; margin: 40px auto; }
        .search-container .input-group-text { background-color: #fff; border-right: none; border-radius: 25px 0 0 25px; padding-left: 20px; }
        .search-container .form-control { border-left: none; border-radius: 0 25px 25px 0; padding: 12px 20px; box-shadow: none !important; }
        .menu-section-title { position: relative; display: inline-block; margin-bottom: 30px; color: #333; }
        .price-tag { color: #ff714d; font-size: 1.1rem; }
        .card { transition: transform 0.3s ease; border-radius: 15px; overflow: hidden; }
        .card:hover { transform: translateY(-5px); }
        .status-badge { position: absolute; top: 12px; right: 12px; font-size: 0.7rem; padding: 5px 12px; border-radius: 50px; font-weight: 600; text-transform: uppercase; z-index: 2; box-shadow: 0 2px 5px rgba(0,0,0,0.2); }
        #search-error { display: none; font-weight: 600; }
        .item-unavailable { opacity: 0.8; }
        .img-unavailable { filter: grayscale(1); }
    </style>
</head>
<body>

<?php
if (isset($_SESSION['userloggedin']) && $_SESSION['userloggedin']) {
    include 'nav-logged.php';
} else {
    include 'navbar.php';
}
?>

<div class="container">
    <div class="search-container">
        <h2 class="text-center mb-4" style="font-family: 'Playfair Display', serif;">Find Your Favorite Food</h2>
        <div class="input-group shadow-sm">
            <span class="input-group-text"><i class="fas fa-search text-muted"></i></span>
            <input type="text" id="search-bar" class="form-control" placeholder="Search dish name (e.g. Pizza, Burger...)">
        </div>
        <div id="search-error" class="text-center mt-4 text-danger">
            <i class="fas fa-search-minus me-2"></i> No item found in our menu!
        </div>
    </div>
</div>

<div id="menu-container">
    <?php foreach ($categories as $category): ?>
        <section id="<?= strtolower(preg_replace('/\s+/', '-', $category['catName'])) ?>" class="container mb-5 category-section">
            <div class="text-center">
                <h1 class="menu-section-title"><?= strtoupper($category['catName']) ?></h1>
            </div>
            <div class="row">
                <?php
                $stmt = $conn->prepare('SELECT * FROM menuitem WHERE catId = ?');
                $stmt->bind_param("i", $category['catId']);
                $stmt->execute();
                $result = $stmt->get_result();
                while ($row = $result->fetch_assoc()):
                    $status = $row['status'];
                    $isUnavailable = ($status == 'Unavailable'); 
                ?>
                    <div class="col-md-6 col-lg-3 mb-4 menu-item">
                        <div class="card h-100 shadow-sm border-0 <?= $isUnavailable ? 'item-unavailable' : '' ?>">
                            <div style="height: 200px; overflow: hidden; position: relative;">
                                <img src="uploads/<?= htmlspecialchars($row['image']) ?>" 
                                     class="card-img-top <?= $isUnavailable ? 'img-unavailable' : '' ?>" 
                                     alt="<?= htmlspecialchars($row['itemName']) ?>" 
                                     style="object-fit: cover; height: 100%; width: 100%;">
                                
                                <?php if ($isUnavailable): ?>
                                    <span class="badge bg-danger status-badge">Not Available</span>
                                <?php else: ?>
                                    <span class="badge bg-success status-badge">Available</span>
                                <?php endif; ?>
                            </div>

                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title fw-bold"><?= htmlspecialchars($row['itemName']) ?></h5>
                                <p class="card-text text-muted small flex-grow-1"><?= htmlspecialchars($row['description']) ?></p>
                                
                                <div class="price-tag mb-3 fw-bold">MMK <?= number_format($row['price']) ?></div>

                                <button class="btn <?= $isUnavailable ? 'btn-secondary' : 'btn-warning' ?> w-100 addItemBtn" <?= $isUnavailable ? 'disabled' : '' ?>>
                                    <?php if ($isUnavailable): ?>
                                        <i class="fas fa-times-circle me-2"></i> Out of Stock
                                    <?php else: ?>
                                        <i class="fas fa-cart-plus me-2"></i> Add to Cart
                                    <?php endif; ?>
                                </button>

                                <input type="hidden" class="pid" value="<?= $row['itemId'] ?>">
                                <input type="hidden" class="pname" value="<?= htmlspecialchars($row['itemName']) ?>">
                                <input type="hidden" class="pprice" value="<?= $row['price'] ?>">
                                <input type="hidden" class="pimage" value="<?= htmlspecialchars($row['image']) ?>">
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </section>
    <?php endforeach; ?>
</div>

<?php if(file_exists('footer.html')) include 'footer.html'; ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
    $('#search-bar').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        var foundAny = false;
        var $errorMsg = $('#search-error');

        if (value === "") {
            $(".menu-item").show();
            $(".category-section").show();
            $errorMsg.hide();
            return;
        }
        $(".menu-item").each(function() {
            var itemName = $(this).find('.card-title').text().toLowerCase();
            if (itemName.indexOf(value) > -1) {
                foundAny = true;
                $(this).show();
            } else {
                $(this).hide();
            }
        });

        if (foundAny) {
            $errorMsg.hide();
            $(".category-section").each(function() {
                var hasVisibleItems = $(this).find('.menu-item:visible').length > 0;
                $(this).toggle(hasVisibleItems);
            });
        } else {
            $errorMsg.fadeIn();
            $(".category-section").hide();
        }
    });
    function userIsLoggedIn() {
        return <?= isset($_SESSION['userloggedin']) && $_SESSION['userloggedin'] ? 'true' : 'false' ?>;
    }

    $(".addItemBtn").click(function(e) {
        e.preventDefault();
        
        if (!userIsLoggedIn()) {
            alert("Please login to add items to cart!");
            window.location.href = 'login.php';
            return;
        }

        var $card = $(this).closest('.card-body');
        var pid = $card.find('.pid').val();
        var pname = $card.find('.pname').val();
        var pprice = $card.find('.pprice').val();
        var pimage = $card.find('.pimage').val();

        $.ajax({
            url: 'action.php',
            method: 'post',
            data: { 
                pid: pid, 
                pname: pname, 
                pprice: pprice, 
                pimage: pimage 
            },
            success: function(response) {
                load_cart_item_number();
                alert(pname + " has been added to your cart!");
            }
        });
    });

    function load_cart_item_number() {
        $.ajax({
            url: 'action.php',
            method: 'get',
            data: { cartItem: "cart_item" },
            success: function(response) {
                $("#cart-item").text(response);
            }
        });
    }

    load_cart_item_number();
});
</script>
</body>
</html>