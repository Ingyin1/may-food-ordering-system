<?php
session_start();

if (!isset($_SESSION['userloggedin']) || !$_SESSION['userloggedin']) {
    header('Location: login.php');
    exit;
}

include 'db_connection.php';
$user_email = $_SESSION['email'] ?? '';
function getUserData($email, $conn) {
    $stmt = $conn->prepare("SELECT firstname, lastname, email, contact, address, profile_image FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fName = $_POST['firstName'];
    $lName = $_POST['lastName'];
    $phone = $_POST['contact'];
    $addr  = $_POST['address'];

    $current_data = getUserData($user_email, $conn);
    $p_image = $current_data['profile_image'];

    if (!empty($_FILES['profile_image']['name'])) {
        $file_name = time() . '_' . $_FILES['profile_image']['name'];
        if(move_uploaded_file($_FILES["profile_image"]["tmp_name"], "uploads/" . $file_name)) {
            $p_image = $file_name;
        }
    }

    $update_stmt = $conn->prepare("UPDATE users SET firstname = ?, lastname = ?, contact = ?, address = ?, profile_image = ? WHERE email = ?");
    $update_stmt->bind_param("ssssss", $fName, $lName, $phone, $addr, $p_image, $user_email);
    
    if ($update_stmt->execute()) {
        header('Location: profile.php?status=success');
        exit;
    }
}

$user_info = getUserData($user_email, $conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Settings | Modern Food</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="profile.css?v=<?= time(); ?>">
</head>
<body>

<?php include 'nav-logged.php'; ?>

<div class="profile-wrapper">
    <div class="profile-card">
        <h2 class="profile-title">Profile <span>Settings</span></h2>

        <?php if(isset($_GET['status']) && $_GET['status'] == 'success'): ?>
            <div class="alert custom-alert">Settings updated successfully!</div>
        <?php endif; ?>

        <form action="profile.php" method="POST" enctype="multipart/form-data">
            <div class="avatar-upload-wrapper">
                <div class="avatar-preview-container">
                    <img src="uploads/<?= htmlspecialchars($user_info['profile_image'] ?? 'default.jpg'); ?>" id="preview" class="avatar-image">
                    <label for="profile_image" class="avatar-edit-icon">
                        <i class="fas fa-camera"></i>
                    </label>
                    <input type="file" id="profile_image" name="profile_image" hidden onchange="previewImg(this)">
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label">First Name</label>
                    <input type="text" name="firstName" class="form-control custom-input" value="<?= htmlspecialchars($user_info['firstname'] ?? ''); ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="lastName" class="form-control custom-input" value="<?= htmlspecialchars($user_info['lastname'] ?? ''); ?>" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Email Address (ReadOnly)</label>
                    <input type="email" class="form-control custom-input readonly-field" value="<?= htmlspecialchars($user_info['email'] ?? ''); ?>" readonly>
                </div>
                <div class="col-12">
                    <label class="form-label">Contact Number</label>
                    <input type="text" name="contact" class="form-control custom-input" value="<?= htmlspecialchars($user_info['contact'] ?? ''); ?>" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Delivery Address</label>
                    <textarea name="address" class="form-control custom-input" rows="3" placeholder="Enter your full address here..."><?= htmlspecialchars($user_info['address'] ?? ''); ?></textarea>
                </div>
            </div>

            <button type="submit" class="btn save-btn w-100">Save Changes</button>
        </form>
    </div>
</div>

<script>
    function previewImg(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) { document.getElementById('preview').src = e.target.result; }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>