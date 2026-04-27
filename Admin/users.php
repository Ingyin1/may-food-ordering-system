<?php
session_start();
if (!isset($_SESSION['adminloggedin'])) {
    header("Location: ../login.php");
    exit();
}
include 'db_connection.php';
$admin_email = $_SESSION['email'] ?? '';
$admin_info = ['firstName' => 'Admin', 'lastName' => '', 'profile_image' => 'default.jpg'];

if ($admin_email) {
    $stmtAdmin = $conn->prepare("SELECT firstName, lastName, profile_image FROM users WHERE email = ?");
    $stmtAdmin->bind_param("s", $admin_email);
    $stmtAdmin->execute();
    $resAdmin = $stmtAdmin->get_result();
    if ($rowAdmin = $resAdmin->fetch_assoc()) {
        $admin_info = [
            'firstName' => $rowAdmin['firstName'],
            'lastName' => $rowAdmin['lastName'],
            'profile_image' => $rowAdmin['profile_image'] ?: 'default.jpg'
        ];
    }
}

$search = $_POST['search'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin User Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="admin_user.css">
    <link rel="stylesheet" href="sidebar.css">
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(4px); 
        }

        .modal.open { display: flex; align-items: center; justify-content: center; }
        .modal-container {
            background: #ffffff;
            width: 100%;
            max-width: 480px;
            border-radius: 20px;
            padding: 35px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            animation: slideUp 0.3s ease-out;
        }

        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .modal-header h2 { font-size: 22px; font-weight: 600; color: #1a1a1a; }

        .close-btn {
            background: none; border: none; font-size: 28px; cursor: pointer; color: #999;
        }
        .modal-field {
            margin-bottom: 22px; 
        }

        .modal-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        .modal-field label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #666;
            margin-bottom: 8px; 
        }
        .modal-field input, 
        .modal-field textarea {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #e1e1e1;
            border-radius: 12px;
            font-size: 15px;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s ease;
            background-color: #f9f9f9;
        }

        .modal-field input:focus {
            border-color: #4361ee;
            background-color: #fff;
            box-shadow: 0 0 0 4px rgba(67, 97, 238, 0.1);
            outline: none;
        }
        .modal-footer {
            display: flex;
            gap: 12px;
            margin-top: 10px;
        }

        .btn-primary, .btn-secondary {
            flex: 1;
            padding: 12px;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: 0.2s;
        }

        .btn-primary { background: #4361ee; color: #fff; }
        .btn-primary:hover { background: #3046c9; }

        .btn-secondary { background: #f0f2f5; color: #666; }
        .btn-secondary:hover { background: #e4e6e9; }
    </style>
</head>

<body>
    <div class="sidebar">
        <button class="close-sidebar" id="closeSidebar">&times;</button>
        <div class="profile-section">
            <img src="../uploads/<?php echo htmlspecialchars($admin_info['profile_image']); ?>" alt="Profile">
            <div class="info">
                <h3>Welcome!</h3>
                <p><?php echo htmlspecialchars($admin_info['firstName'] . ' ' . $admin_info['lastName']); ?></p>
            </div>
        </div>
        <ul>
            <li><a href="index.php"><i class="fas fa-chart-line"></i> Overview</a></li>
            <li><a href="admin_menu.php"><i class="fas fa-utensils"></i> Menu</a></li>
            <li><a href="admin_orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
            <li><a href="users.php" class="active"><i class="fas fa-users"></i> Users</a></li>
            <li><a href="reviews.php"><i class="fas fa-star"></i> Reviews</a></li>
            <li><a href="profile.php"><i class="fas fa-user"></i> Profile Setting</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <div class="content">
        <div class="header">
            <button id="toggleSidebar" class="toggle-button"><i class="fas fa-bars"></i></button>
            <h2><i class="fas fa-users"></i> User List</h2>
        </div>

        <div class="actions">
            <button onclick="openaddUserModal()"><i class="fas fa-user-plus"></i> Add User</button>
            <form method="POST" id="searchForm" class="search-bar">
                <input type="text" name="search" placeholder="Search by name or email..." value="<?php echo htmlspecialchars($search); ?>">
            </form>
        </div>

        <table id="userTable">
            <thead>
                <tr>
                    <th>NO</th>
                    <th>Date Created</th>
                    <th>Email</th>
                    <th>Full Name</th>
                    <th>Contact</th>
                    <th>Address</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($search)) {
                    $searchTerm = '%' . $search . '%';
                    $stmt = $conn->prepare("SELECT * FROM users WHERE email LIKE ? OR firstName LIKE ? OR lastName LIKE ?");
                    $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
                    $stmt->execute();
                    $result = $stmt->get_result();
                } else {
                    $result = $conn->query("SELECT * FROM users");
                }

                $counter = 1;
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>{$counter}</td>
                        <td>{$row['dateCreated']}</td>
                        <td>" . htmlspecialchars($row['email']) . "</td>
                        <td>" . htmlspecialchars($row['firstName'] . ' ' . $row['lastName']) . "</td>
                        <td>" . htmlspecialchars($row['contact']) . "</td>
                        <td class='address-col'>" . htmlspecialchars($row['address'] ?? '-') . "</td>
                        <td>
                            <button id='editbtn' onclick='openEditUserModal(this)' 
                                data-email='".htmlspecialchars($row['email'])."' 
                                data-firstname='".htmlspecialchars($row['firstName'])."' 
                                data-lastname='".htmlspecialchars($row['lastName'])."' 
                                data-contact='".htmlspecialchars($row['contact'])."' 
                                data-address='".htmlspecialchars($row['address'] ?? '')."'>
                                <i class='fas fa-edit'></i>
                            </button>
                            <button id='deletebtn' onclick=\"deleteItem('{$row['email']}')\"><i class='fas fa-trash'></i></button>
                        </td>
                    </tr>";
                    $counter++;
                }
                ?>
            </tbody>
        </table>
    </div>

    <div id="addUserModal" class="modal">
        <div class="modal-container">
            <form action="add_user.php" method="POST">
                <div class="modal-header">
                    <h2>Add New User</h2>
                    <button type="button" class="close-btn" onclick="closeaddUserModal()">&times;</button>
                </div>
                
                <div class="modal-body">
                    <div class="modal-field">
                        <label>Email Address</label>
                        <input type="email" name="email" placeholder="example@gmail.com" required>
                    </div>

                    <div class="modal-row">
                        <div class="modal-field">
                            <label>First Name</label>
                            <input type="text" name="firstName" placeholder="John" required>
                        </div>
                        <div class="modal-field">
                            <label>Last Name</label>
                            <input type="text" name="lastName" placeholder="Doe" required>
                        </div>
                    </div>

                    <div class="modal-field">
                        <label>Contact Number</label>
                        <input type="text" name="contact" placeholder="09xxxxxxxxx" required>
                    </div>

                    <div class="modal-field">
                        <label>Password</label>
                        <input type="password" name="password" placeholder="••••••••" required>
                    </div>

                    <div class="modal-field">
                        <label>Address</label>
                        <textarea name="address" rows="3" placeholder="Enter full address..."></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeaddUserModal()">Cancel</button>
                    <button type="submit" class="btn-primary">Create User</button>
                </div>
            </form>
        </div>
    </div>

    <div id="editUserModal" class="modal">
        <div class="modal-container">
            <form action="edit_user.php" method="POST">
                <div class="modal-header"><h2>Edit User</h2><span class="close-icon" onclick="closeEditUserModal()">&times;</span></div>
                <div class="modal-content">
                    <div class="input-group"><input type="email" name="email" id="editEmail" class="input" readonly><label class="label">Email</label></div>
                    <div class="input-group"><input type="text" name="firstName" id="editFirstName" class="input" required><label class="label">First Name</label></div>
                    <div class="input-group"><input type="text" name="lastName" id="editLastName" class="input" required><label class="label">Last Name</label></div>
                    <div class="input-group"><input type="text" name="contact" id="editContact" class="input" required><label class="label">Contact</label></div>
                    <div class="input-group"><textarea name="address" id="editAddress" class="input" required></textarea><label class="label">Address</label></div>
                </div>
                <div class="modal-footer"><button type="button" onclick="closeEditUserModal()">Cancel</button><button type="submit">Update</button></div>
            </form>
        </div>
    </div>

    <script>
        function openaddUserModal() { document.getElementById('addUserModal').classList.add('open'); }
        function closeaddUserModal() { document.getElementById('addUserModal').classList.remove('open'); }
        function closeEditUserModal() { document.getElementById('editUserModal').classList.remove('open'); }

        function openEditUserModal(button) {
            document.getElementById('editEmail').value = button.getAttribute('data-email');
            document.getElementById('editFirstName').value = button.getAttribute('data-firstname');
            document.getElementById('editLastName').value = button.getAttribute('data-lastname');
            document.getElementById('editContact').value = button.getAttribute('data-contact');
            document.getElementById('editAddress').value = button.getAttribute('data-address');
            document.getElementById('editUserModal').classList.add('open');
        }

        function deleteItem(email) {
            if (confirm('Are you sure you want to delete ' + email + '?')) {
                const xhr = new XMLHttpRequest();
                xhr.open("POST", "delete_user.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onload = function() { location.reload(); };
                xhr.send("email=" + encodeURIComponent(email));
            }
        }
        document.querySelector('input[name="search"]').addEventListener('keyup', function() {
            const query = this.value;
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'search_users.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (this.status === 200) document.querySelector('#userTable tbody').innerHTML = this.responseText;
            };
            xhr.send('search=' + encodeURIComponent(query));
        });

        const toggleBtn = document.getElementById('toggleSidebar');
        const sidebar = document.querySelector('.sidebar');
        if(toggleBtn) toggleBtn.addEventListener('click', () => sidebar.classList.toggle('active'));
    </script>
</body>
</html>