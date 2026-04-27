<?php
session_start();
if (!isset($_SESSION['adminloggedin'])) {
    header("Location: ../login.php");
    exit();
}

include 'db_connection.php';
include 'sidebar.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Menu Management</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="sidebar.css">
    <link rel="stylesheet" href="admin_menu.css">
</head>

<body>
    <div class="sidebar">
        <button class="close-sidebar" id="closeSidebar">&times;</button>

        <div class="profile-section">
            <img src="../uploads/<?php echo htmlspecialchars($admin_info['profile_image']); ?>" alt="Profile Picture">
            <div class="info">
                <h3>Welcome Back!</h3>
                <p><?php echo htmlspecialchars($admin_info['firstName']) . ' ' . htmlspecialchars($admin_info['lastName']); ?></p>
            </div>
        </div>

        <ul>
            <li><a href="index.php"><i class="fas fa-chart-line"></i> Overview</a></li>
            <li><a href="admin_menu.php" class="active"><i class="fas fa-utensils"></i> Menu Management</a></li>
            <li><a href="admin_orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
            <li><a href="reservations.php"><i class="fas fa-calendar-alt"></i> Reservations</a></li>
            <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
            <li><a href="reviews.php"><i class="fas fa-star"></i> Reviews</a></li>
            <li><a href="profile.php"><i class="fas fa-user"></i> Profile Setting</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <div class="content">
        <div class="header">
            <button id="toggleSidebar" class="toggle-button">
                <i class="fas fa-bars"></i>
            </button>
            <h2><i class="fas fa-utensils"></i> Menu Management</h2>
        </div>
        <div class="modal-row">
            <div>
                <button onclick="openModal()"><i class="fas fa-plus"></i> &nbsp;Add New Category</button>
                <button onclick="openItemModal()"> <i class="fas fa-plus"></i> &nbsp;Add New Item</button>
                <button onclick="openViewCategoryModal()"> <i class="fas fa-eye"></i> &nbsp;View Categories</button>
            </div>
            <div class="search-bar">
                <select id="categoryFilter" onchange="filterCategories()">
                    <option value="">All Categories</option>
                    <?php
                    $catResult = $conn->query("SELECT catname FROM menucategory");
                    $categories = [];
                    while ($cat = $catResult->fetch_assoc()) {
                        $categories[] = $cat;
                        echo "<option value='".htmlspecialchars($cat['catname'])."'>".htmlspecialchars($cat['catname'])."</option>";
                    }
                    ?>
                </select>
            </div>
        </div>

        <table id="menuTable">
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Image</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Popular</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $menuResult = $conn->query("SELECT mi.*, mc.catname FROM menuitem mi JOIN menucategory mc ON mi.catid = mc.catid");
                $menuItems = [];
                if ($menuResult) {
                    while ($row = $menuResult->fetch_assoc()) {
                        $menuItems[] = $row;
                    }
                }

                if (count($menuItems) > 0) {
                    foreach ($menuItems as $row) {
                        $isPopularChecked = $row['is_popular'] ? 'checked' : '';
                        echo "<tr data-category='".htmlspecialchars($row['catname'])."'>
                <td>".htmlspecialchars($row['itemName'])."</td> 
                <td><img src='../uploads/".htmlspecialchars($row['image'])."' alt='Item' width='50'></td>
                <td>".htmlspecialchars($row['description'])."</td>
                <td>Rs ".htmlspecialchars($row['price'])."</td>
                <td>".htmlspecialchars($row['catname'])."</td>
                <td>".htmlspecialchars($row['status'])."</td>
                <td>
                    <div class='toggler'>
                        <input id='toggler-{$row['itemId']}' name='toggler-{$row['itemId']}' type='checkbox' value='1' $isPopularChecked onchange='togglePopular({$row['itemId']}, this)'>
                        <label for='toggler-{$row['itemId']}'>
                            <svg class='toggler-on' version='1.1' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 130.2 130.2'>
                                <polyline class='path check' points='100.2,40.2 51.5,88.8 29.8,67.5'></polyline>
                            </svg>
                            <svg class='toggler-off' version='1.1' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 130.2 130.2'>
                                <line class='path line' x1='34.4' y1='34.4' x2='95.8' y2='95.8'></line>
                                <line class='path line' x1='95.8' y1='34.4' x2='34.4' y2='95.8'></line>
                            </svg>
                        </label>
                    </div>
                </td>
                <td>
                    <button id='editbtn' onclick='openEditItemModal(this)' 
                        data-itemid='{$row['itemId']}' 
                        data-itemname='".htmlspecialchars($row['itemName'])."' 
                        data-description='".htmlspecialchars($row['description'])."' 
                        data-price='{$row['price']}' 
                        data-image='{$row['image']}' 
                        data-category='".htmlspecialchars($row['catname'])."' 
                        data-status='{$row['status']}'>
                        <i class='fas fa-edit'></i>
                    </button>   
                    <button id='deletebtn' onclick=\"deleteItem('{$row['itemId']}')\">
                        <i class='fas fa-trash'></i>
                    </button>
                </td>
              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='8' style='text-align: center;'>No menu items found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <div class="modal" id="categoryModal">
        <div class="modal-overlay"></div>
        <div class="modal-container">
            <form class="form" method="POST" action="add_category.php">
                <div class="modal-header">
                    <h2>Add New Category</h2>
                    <span class="close-icon" onclick="closeModal()">&times;</span>
                </div>
                <div class="modal-content">
                    <div class="input-group">
                        <input type="text" name="catName" id="catName" class="input" required>
                        <label for="catName" class="label">Category Name</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="button" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="button">Save</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal" id="itemModal">
        <div class="modal-overlay"></div>
        <div class="modal-container">
            <form class="form" method="POST" action="add_item.php" enctype="multipart/form-data">
                <div class="modal-header">
                    <h2>Add New Item</h2>
                    <span class="close-icon" onclick="closeItemModal()">&times;</span>
                </div>
                <div class="modal-content">
                    <div class="input-group">
                        <input type="text" name="itemName" id="itemName" class="input" required>
                        <label for="itemName" class="label">Item Name</label>
                    </div>
                    <div class="input-group">
                        <input type="text" name="description" id="description" class="input" required>
                        <label for="description" class="label">Description</label>
                    </div>
                    <div class="input-group">
                        <select name="status" id="status" class="input" required>
                            <option value="">Status</option>
                            <option value="Available">Available</option>
                            <option value="Unavailable">Unavailable</option>
                        </select>
                        <label for="status" class="label">Status</label>
                    </div>
                    <div class="input-group">
                        <input type="number" name="price" id="price" class="input" required>
                        <label for="price" class="label">Price</label>
                    </div>
                    <<div class="input-group">
                        <select name="catId" id="catId" class="input" required>
                            <option value="">Select Category</option>
                            <?php
                            $catQuery = $conn->query("SELECT catid, catname FROM menucategory");
                            while ($cat = $catQuery->fetch_assoc()) {
                                echo "<option value='{$cat['catid']}'>{$cat['catname']}</option>";
                            }
                            ?>
                        </select>
                        <label for="catId" class="label">Category</label>
                    </div>
                    <div class="input-group">
                        <input type="file" name="image" id="image" class="input" accept="image/*" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="button" onclick="closeItemModal()">Cancel</button>
                    <button type="submit" class="button">Save</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal" id="editItemModal">
        <div class="modal-overlay"></div>
        <div class="modal-container">
            <form class="form" method="POST" action="edit_item.php" enctype="multipart/form-data">
                <div class="modal-header">
                    <h2>Edit Item</h2>
                    <span class="close-icon" onclick="closeEditItemModal()">&times;</span>
                </div>
                <div class="modal-content">
                    <input type="hidden" name="itemId" id="editItemId">
                    <input type="hidden" name="existingImage" id="editExistingImage">
                    <div class="input-group">
                        <input type="text" name="itemName" id="editItemName" class="input" required>
                        <label for="editItemName" class="label">Item Name</label>
                    </div>
                    <div class="input-group">
                        <input type="text" name="description" id="editDescription" class="input" required>
                        <label for="editDescription" class="label">Description</label>
                    </div>
                    <div class="input-group">
                        <select name="status" id="editStatus" class="input" required>
                            <option value="Available">Available</option>
                            <option value="Unavailable">Unavailable</option>
                        </select>
                        <label for="editStatus" class="label">Status</label>
                    </div>
                    <div class="input-group">
                        <input type="number" name="price" id="editPrice" class="input" required>
                        <label for="editPrice" class="label">Price</label>
                    </div>
                    <div class="input-group">
                        <select name="catId" id="editCatId" class="input" required>
                            <?php
                            $catQuery = $conn->query("SELECT catid, catname FROM menucategory");
                            while ($cat = $catQuery->fetch_assoc()) {
                                echo "<option value='{$cat['catid']}'>{$cat['catname']}</option>";
                            }
                            ?>
                        </select>
                        <label for="editCatId" class="label">Category</label>
                    </div>

                    <div class="input-group">
                        <input type="file" name="image" id="editImage" class="input" accept="image/*">
                        <small>Leave empty if not changing</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="button" onclick="closeEditItemModal()">Cancel</button>
                    <button type="submit" class="button">Save</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal" id="viewCategoryModal">
        <div class="modal-overlay"></div>
        <div class="modal-container" style="background: #ffffff;">
            <div class="modal-header" style=" border-bottom: 1px solid #ffffff">
                <h2>Categories</h2>
                <span class="close-icon" onclick="closeViewCategoryModal()">&times;</span>
            </div>
            <div class="modal-content">
                <div class="input-group">
                    <table id="categoryTable" style="width:100%; border-collapse: collapse;">
                        <thead>
                            <tr>
                                <th>Category Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (count($categories) > 0) {
                                foreach ($categories as $cat) {
                                    echo "<tr>";
                                    echo "<td>{$cat['catname']}</td>";
                                    echo "<td><button class='delete-btn' onclick=\"deleteCategory('{$cat['catname']}')\"><i class='fas fa-trash'></i></button></td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='2'>No categories found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer" style="border-top: 1px solid #ffc9b3">
                <button type="button" class="button" onclick="closeViewCategoryModal()">Close</button>
            </div>
        </div>
    </div>

    <script src="sidebar.js"></script>
    <script>
        function openModal() { document.getElementById('categoryModal').classList.add('open'); }
        function closeModal() { document.getElementById('categoryModal').classList.remove('open'); }
        function openItemModal() { document.getElementById('itemModal').classList.add('open'); }
        function closeItemModal() { document.getElementById('itemModal').classList.remove('open'); }
        function openViewCategoryModal() { document.getElementById('viewCategoryModal').classList.add('open'); }
        function closeViewCategoryModal() { document.getElementById('viewCategoryModal').classList.remove('open'); }

        function openEditItemModal(button) {
            document.getElementById('editItemId').value = button.getAttribute('data-itemid');
            document.getElementById('editItemName').value = button.getAttribute('data-itemname');
            document.getElementById('editDescription').value = button.getAttribute('data-description');
            document.getElementById('editPrice').value = button.getAttribute('data-price');
            document.getElementById('editStatus').value = button.getAttribute('data-status');
            document.getElementById('editCatId').value = button.getAttribute('data-catid');
            document.getElementById('editExistingImage').value = button.getAttribute('data-image');
            document.getElementById('editItemModal').classList.add('open');
        }

        function closeEditItemModal() { document.getElementById('editItemModal').classList.remove('open'); }

        function togglePopular(itemId, checkbox) {
            var isPopular = checkbox.checked ? 1 : 0;
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "update_popular_status.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.send("itemId=" + itemId + "&is_popular=" + isPopular);
        }

        function deleteItem(itemId) {
            if (confirm("Are you sure you want to delete this item?")) {
                window.location.href = `delete_item.php?id=${itemId}`;
            }
        }

        function filterCategories() {
            const category = document.getElementById('categoryFilter').value;
            const rows = document.querySelectorAll('#menuTable tbody tr');
            rows.forEach(row => {
                row.style.display = (category === "" || row.dataset.category === category) ? '' : 'none';
            });
        }

        function deleteCategory(catName) {
            if (confirm("Are you sure you want to delete the category: " + catName + "? This might affect items in this category.")) {
                window.location.href = `delete_category.php?name=${encodeURIComponent(catName)}`;
            }
        }
    </script>
</body>
</html>