<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Q&A | May Food</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="index.css">
    <style>
        body {
            background: #fef7f2;
            font-family: 'Poppins', sans-serif;
        }
        .qa-hero {
            padding-top: 10px;
            padding-bottom: 40px;
            background: linear-gradient(135deg, #ffffff 0%, #ffffff 100%);
        }
        .qa-tag {
            display: inline-block;
            padding: 8px 14px;
            background: #ffffffaa;
            border-radius: 999px;
            font-weight: 600;
            color: #ff825c;
            letter-spacing: 0.5px;
        }
        .qa-title {
            font-weight: 700;
            color: #3b2c29;
        }
        .qa-subtitle {
            color: #5a4a46;
            max-width: 720px;
            margin: 12px auto 0;
        }
        .accordion-button:not(.collapsed) {
            color: #c45b3b;
            background-color: #fff6f0;
            box-shadow: none;
        }
        .accordion-button:focus {
            box-shadow: none;
            border-color: #ffd0b8;
        }
        .accordion-item {
            border-radius: 14px !important;
            overflow: hidden;
            border: 1px solid #f1d7c9;
            margin-bottom: 12px;
            box-shadow: 0 6px 16px rgba(0,0,0,0.04);
        }
        .support-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 24px rgba(0,0,0,0.06);
            background: #fff;
        }
        .support-card h5 {
            color: #3b2c29;
            font-weight: 600;
        }
        .support-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            border-radius: 12px;
            background: #fff6f0;
            color: #4a3b38;
            font-weight: 500;
        }
        .cta-btn {
            background: linear-gradient(135deg, #ff9a76, #ff7c62);
            border: none;
            color: white;
            font-weight: 600;
            box-shadow: 0 10px 24px rgba(255, 124, 98, 0.25);
        }
        .cta-btn:hover {
            color: white;
            filter: brightness(0.96);
        }
        .qa-badge {
            padding: 4px 10px;
            background: #ffe8d7;
            color: #8a5b3d;
            border-radius: 999px;
            font-size: 0.85rem;
            font-weight: 600;
        }
    </style>
</head>
<body>
<?php
if (isset($_SESSION['userloggedin']) && $_SESSION['userloggedin']) {
    include 'nav-logged.php';
} else {
    include 'navbar.php';
}

$faqs = [
    ['question' => 'What are your opening hours?', 'answer' => 'We are open daily from 9:00 AM to 10:00 PM. We remain open during public holidays to serve you better.'],
    ['question' => 'How long does it take to prepare my order?', 'answer' => 'For delivery and dine-in, please allow a minimum of 10 minutes for preparation. Time may vary based on the type and quantity of items ordered.'],
    ['question' => 'What payment methods do you accept?', 'answer' => 'We accept MPU, Visa, Mastercard, KBZPay, WavePay, and Cash on Delivery. You can select your preferred method during checkout.'],
    ['question' => 'How can I cancel my order?', 'answer' => 'Orders can be cancelled within 5 minutes of placement, provided they have not entered the preparation stage. For advanced bookings, please contact our support team.'],
    ['question' => 'What is the estimated delivery time?', 'answer' => 'Estimated delivery to Downtown, Sanchaung, Kamayut, and Hlaing townships is between 30 to 50 minutes. Times may be longer during peak hours or heavy rain.'],
    ['question' => 'Do you accept bulk orders or catering?', 'answer' => 'Yes! For corporate events, team lunches, or birthday catering, please contact us at least one day in advance. We offer customizable menus and special price packages.']
];
?>

<section class="qa-hero text-center">
    <div class="container">
        <span class="qa-tag">Got questions?</span>
        <h1 class="qa-title mt-3">Frequently Asked Questions</h1>
        <p class="qa-subtitle">Find answers to common questions here. If you can't find what you're looking for, feel free to contact our support team below.</p>
    </div>
</section>

<div class="container my-5">
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="mb-0">Popular Questions</h5>
                <span class="qa-badge"><i class="fa-regular fa-circle-question me-1"></i> Updated weekly</span>
            </div>
            <div class="accordion" id="faqAccordion">
                <?php foreach ($faqs as $index => $faq): ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading<?= $index ?>">
                            <button class="accordion-button <?= $index === 0 ? '' : 'collapsed' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $index ?>" aria-expanded="<?= $index === 0 ? 'true' : 'false' ?>" aria-controls="collapse<?= $index ?>">
                                <?= htmlspecialchars($faq['question']) ?>
                            </button>
                        </h2>
                        <div id="collapse<?= $index ?>" class="accordion-collapse collapse <?= $index === 0 ? 'show' : '' ?>" aria-labelledby="heading<?= $index ?>" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <?= htmlspecialchars($faq['answer']) ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card support-card p-4 h-100">
                <h5 class="mb-3">Still need help?</h5>
                <p class="text-muted">Our support team is here for you. For quick assistance, contact us via Messenger, Phone, or Email.</p>
                <div class="d-flex flex-column gap-3">
                    <div class="support-chip">
                        <i class="fa-solid fa-phone"></i>
                        <span>09 777 222 888 (9AM - 10PM)</span>
                    </div>
                    <div class="support-chip">
                        <i class="fa-regular fa-envelope"></i>
                        <span>hello@mayfood.com</span>
                    </div>
                    <div class="support-chip">
                        <i class="fa-regular fa-comment-dots"></i>
                        <span>Messenger Live Chat</span>
                    </div>
                </div>
                <div class="mt-4 d-grid gap-2">
                    <a class="btn cta-btn" href="index.php#Reservation">Make a reservation</a>
                    <a class="btn btn-outline-dark" href="menu.php">Order from menu</a>
                </div>
                <p class="small text-muted mt-3 mb-0">Typical response time: ~5 mins during business hours.</p>
            </div>
        </div>
    </div>
</div>

<?php include_once('footer.html'); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>