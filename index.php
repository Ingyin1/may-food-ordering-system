<?php
session_start();
include 'db_connection.php';
$sql = "SELECT itemId, itemName, image, price FROM menuitem WHERE is_popular = TRUE";
$result = $conn->query($sql);
$popularItems = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $popularItems[] = $row;
    }
}
$reviews = [];
$review_sql = "SELECT first_name, rating, review_text 
               FROM reviews 
               WHERE status = 'approved' 
               ORDER BY created_at DESC 
               LIMIT 12";

$review_result = $conn->query($review_sql);

if ($review_result && $review_result->num_rows > 0) {
    while ($row = $review_result->fetch_assoc()) {
        $reviews[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Home</title>
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  <link rel="stylesheet" href="index.css?v=<?php echo time(); ?>">
</head>

<body>
  <?php
  if (isset($_SESSION['userloggedin']) && $_SESSION['userloggedin']) {
    include 'nav-logged.php';
  } else {
    include 'navbar.php';
  }
  ?>

  <div class="main">
    <section>
      <div class="container mt-3">
        <div class="row d-flex justify-content-start align-items-start main-container">
          <div class="col-md-5 col-sm-12 col-lg-5 reveal main-text mb-4 text-align-justify mt-5" data-aos="fade-up">
            <h2>Welcome to <span style="color: #ff714d;"> May Food,</span></h2>
            <h4 style="color: #3E2723; font-weight: 450;">"Where Hot Flavors Meet Cool Comfort."</h4>
            <p style="font-size: 18px; text-align: justify;">
              Dive into a culinary celebration where every dish bursts with
              flavor. At May Food, we believe in making every meal an
              unforgettable experience. Whether you're here for a casual meal or a
              special occasion, our vibrant dishes will leave a lasting
              impression.
            </p>
            <div class="buttondiv">
              <div>
                <a href="menu.php">
                  <button class="button">
                    Start Order
                    <svg class="cartIcon" viewBox="0 0 576 512">
                      <path d="M0 24C0 10.7 10.7 0 24 0H69.5c22 0 41.5 12.8 50.6 32h411c26.3 0 45.5 25 38.6 50.4l-41 152.3c-8.5 31.4-37 53.3-69.5 53.3H170.7l5.4 28.5c2.2 11.3 12.1 19.5 23.6 19.5H488c13.3 0 24 10.7 24 24s-10.7 24-24 24H199.7c-34.6 0-64.3-24.6-70.7-58.5L77.4 54.5c-.7-3.8-4-6.5-7.9-6.5H24C10.7 48 0 37.3 0 24zM128 464a48 48 0 1 1 96 0 48 48 0 1 1 -96 0zm336-48a48 48 0 1 1 0 96 48 48 0 1 1 0-96z"></path>
                    </svg>
                  </button>
                </a>
              </div>
              <div>
                <a class="button1" href="menu.php">
                  <span class="button__icon-wrapper">
                    <svg width="10" class="button__icon-svg" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 15">
                      <path fill="currentColor" d="M13.376 11.552l-.264-10.44-10.44-.24.024 2.28 6.96-.048L.2 12.56l1.488 1.488 9.432-9.432-.048 6.912 2.304.024z"></path>
                    </svg>
                    <svg class="button__icon-svg button__icon-svg--copy" xmlns="http://www.w3.org/2000/svg" width="10" fill="none" viewBox="0 0 14 15">
                      <path fill="currentColor" d="M13.376 11.552l-.264-10.44-10.44-.24.024 2.28 6.96-.048L.2 12.56l1.488 1.488 9.432-9.432-.048 6.912 2.304.024z"></path>
                    </svg>
                  </span>
                  Explore Menu
                </a>
              </div>
            </div>
          </div>
          <div class="col-md-7 col-sm-12 col-lg-7 d-flex justify-content-center align-items-start slide-in-right main-image">
            <img src="images/Pizza.png" class="img" style=" width: 85%; height: 80%;">
          </div>
        </div>
        <div class="row">
          <section>
            <div class="menu-section">
              <div class="container-fluid">
                <div class="row">
                  <div class="row d-flex justify-content-center align-items-center mb-4 font-weight-bold" id="text">
                    <h1 style="color: #3E2723;">OUR <span style="color: #ff714d;">MENU</span></h1>
                  </div>
                  <div class="col-lg-3 col-md-6 mb-4">
                    <div class="category-card" style="background-image: url('images/Main_Xaing Gou.jpg');" data-aos="fade-up">
                      <div class="card-overlay">
                        <div class="overlay-content">
                          <h3>Malar Series</h3>
                            <p>Feel the authentic spice and numbing sensation of our signature Malar dishes.</p>
                            <a href="menu.php#malar-series" class="explore-btn">Taste the Heat</a>
                        </div>
                      </div>
                      <div class="card-bottom">
                          <h3>Malar Series</h3>
                          <button class="explore-btn">Explore Variety</button></a>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-3 col-md-6 mb-4">
                    <div class="category-card" style="background-image: url('images/Main_noodle.jpg');" data-aos="fade-up">
                      <div class="card-overlay">
                        <div class="overlay-content">
                          <h3>Noodles</h3>
                          <p>From stir-fried to comforting soups, discover our variety of handcrafted noodles.</p>
                          <a href="menu.php#noodle-series" class="explore-btn">View Noodles</a>
                        </div>
                      </div>
                      <div class="card-bottom">
                        <h3>Noodles Series</h3>
                        <button class="explore-btn">Explore Variety</button></a>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-3 col-md-6 mb-4">
                    <div class="category-card" style="background-image: url('images/Main_snack.jpg');" data-aos="fade-up">
                      <div class="card-overlay">
                        <div class="overlay-content">
                          <h3>Snacks</h3>
                          <p>Perfect crispy bites and savory appetizers to share with your friends and family.</p>
                          <a href="menu.php#snacks-series" class="explore-btn">Grab a Bite</a>
                        </div>
                      </div>
                      <div class="card-bottom">
                        <h3>Snacks Series</h3>
                        <button class="explore-btn">Explore Variety</button></a>
                      </div>
                    </div>
                  </div>

                  <div class="col-lg-3 col-md-6 mb-4">
                    <div class="category-card" style="background-image: url('images/Main_drink.jpg');" data-aos="fade-up">
                      <div class="card-overlay">
                        <div class="overlay-content">
                            <h3>Drinks</h3>
                            <p>Refresh your day with our selection of cool mocktails, juices, and specialty teas.</p>
                            <a href="menu.php#drink-series" class="explore-btn">Stay Refreshed</a>
                        </div>
                      </div>
                      <div class="card-bottom">
                        <h3>Drinks Series</h3>
                          <button class="explore-btn">Explore Variety</button></a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </section>
        </div>
      </div>
    </section>
  </div>
  <section class="why-choose-us" id="why-choose-us">
  <div class="container">
    <div class="row text-center why-us-header" data-aos="fade-up">
      <div class="col-12">
        <h1 style="color: #3E2723;">WHY <span style="color: #ff714d;">CHOOSE US?</span></h1>
        <p class="lead text-muted">We provide the best dining and delivery experience with premium quality.</p>
      </div>
    </div>

    <div class="row g-4">
      <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
        <div class="feature-card">
          <div class="feature-icon-circle">
            <img src="icons/delivery-man.png" alt="Fast Delivery">
          </div>
          <h4>Fast Delivery</h4>
          <p>Your food arrives hot and fresh, exactly when you expect it.</p>
        </div>
      </div>

      <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
        <div class="feature-card">
          <div class="feature-icon-circle">
            <img src="icons/vegetables.png" alt="Fresh Ingredients">
          </div>
          <h4>Fresh Quality</h4>
          <p>We source only the finest organic ingredients for every meal.</p>
        </div>
      </div>

      <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
        <div class="feature-card">
          <div class="feature-icon-circle">
            <img src="icons/waiter (1).png" alt="Friendly Service">
          </div>
          <h4>Best Service</h4>
          <p>Experience hospitality that makes you feel right at home.</p>
        </div>
      </div>

      <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
        <div class="feature-card">
          <div class="feature-icon-circle">
            <img src="icons/tasty.png" alt="Exceptional Taste">
          </div>
          <h4>Great Taste</h4>
          <p>Crafted by expert chefs to give you a blast of authentic flavors.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="popular reveal" data-aos="fade-up">
  <div class="container">
    <h1 class="text-center mt-3"style="color: #3E2723;">OUR <span style="color: #ff714d;">TOP PICKS</span></h1>
    <p class="text-center" style="font-size: 1.2rem; color: #64748b;">~ Handpicked meals that are a hit with everyone ~</p>

    <div class="owl-carousel top-picks-carousel mt-4">
      <?php foreach ($popularItems as $item): ?>
        <div class="item px-2">
          <div class="card h-100 shadow-sm border-0 custom-food-card">
            <div class="img-container" style="height: 200px; overflow: hidden;">
              <img src="uploads/<?php echo htmlspecialchars($item['image']); ?>" 
                   class="card-img-top" 
                   alt="<?php echo htmlspecialchars($item['itemName']); ?>"
                   style="object-fit: cover; height: 100%; width: 100%;">
            </div>
            <div class="card-body d-flex flex-column text-center">
              <h5 class="card-title fw-bold" style="font-size: 1.1rem;"><?php echo htmlspecialchars($item['itemName']); ?></h5>
              <p class="card-text text-warning fw-bold">MMK <?php echo number_format($item['price']); ?></p>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
  <section class="about-section" id="About-Us">
    <div class="container">
        <div class="about-grid">
            
            <div class="about-img-container" data-aos="fade-right">
                <img src="images/Burger.png" alt="Our Story">
            </div>

            <div class="about-content" data-aos="fade-left">
                <span class="about-header-tag"style="color: #ff9a3c;">Since 2020</span>
                <h1 class="about-title">We provide healthy food for your <span style="color: #ff9a3c;">family.</span></h1>
                
                <p class="about-text">
                    At <strong>May Food</strong>, we believe that great food starts with simple ingredients and a lot of heart. Our journey began with a passion for bringing families together over meals that are both delicious and wholesome. 
                </p>

                <div class="about-stats">
                    <div class="stat-item">
                        <h3 style="color: #ffdd19;">5+</h3>
                        <p>Years Experience</p>
                    </div>
                    <div class="stat-item">
                        <h3 style="color: #ffdd19;">50+</h3>
                        <p>Popular Dishes</p>
                    </div>
                    <div class="stat-item">
                        <h3 style="color: #ffdd19;">100%</h3>
                        <p>Fresh Quality</p>
                    </div>
                </div>

                <div class="buttondiv">
                    <a href="menu.php" class="button1" style="border-radius: 5px;">
                        See Our Menu
                        <div class="button__icon-wrapper">
                            <i class="fas fa-arrow-right" style="font-size: 12px;"></i>
                        </div>
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>
        
<section class="review-section py-5" id="reviews" style="background: #fcfcfc;">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="badge rounded-pill bg-light text-orange px-3 py-2 mb-2 shadow-sm" style="color: #ff714d; border: 1px solid #ff714d20;">What's Our Guest Say</span>
            <h2 class="display-5 fw-bold"style="color: #3E2723;">Customer <span style="color: #ff714d;">Stories</span></h2>
            <div class="mx-auto" style="width: 50px; height: 3px; background: #ff714d; border-radius: 10px;"></div>
        </div>
        
        <div class="owl-carousel clients-carousel p-3">
            <?php if (!empty($reviews)): ?>
                <?php foreach ($reviews as $rev): ?>
                    <div class="item">
                        <div class="modern-review-card">
                            <div class="quote-icon-top">
                                <i class="fas fa-quote-left"></i>
                            </div>
                            <div class="review-body">
                                <p class="review-text">"<?php echo htmlspecialchars($rev['review_text']); ?>"</p>
                            </div>
                            <div class="review-footer">
                                <div class="reviewer-meta">
                                    <h5 class="reviewer-name"><?php echo htmlspecialchars($rev['first_name']); ?></h5>
                                    <div class="stars">
                                        <?php 
                                        for ($i = 1; $i <= 5; $i++) {
                                            echo ($i <= $rev['rating']) 
                                                ? '<i class="fas fa-star gold-star"></i>' 
                                                : '<i class="far fa-star gray-star"></i>';
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>
 
 <section class="table-reservation py-5" id="Reservation">
  <div class="container">
    <div class="row text-center mb-5" data-aos="fade-up">
      <h1 class="display-4 fw-bold" style="color: #3E2723;">TABLE <span style="color: #ff714d;">RESERVATION</span></h1>
      <p class="lead text-muted">Book your dining experience with us and enjoy a delightful meal.</p>
    </div>

    <div class="reservation-card shadow-lg border-0" data-aos="zoom-in" style="border-radius: 20px; overflow: hidden; background: #fff;">
      <div class="row g-0">
        <div class="col-lg-6 d-none d-lg-block">
          <div class="reservation-img-wrapper" style="background: url('images/table.jpg') center/cover no-repeat; min-height: 100%; min-height: 600px; position: relative;">
            <div class="img-content p-5 d-flex flex-column justify-content-end h-100" style="background: linear-gradient(to top, rgba(0,0,0,0.8), transparent); position: absolute; bottom: 0; width: 100%;">
                <h3 class="text-white">Reserved for You</h3>
                <p class="text-white-50">Experience the finest flavors in a cozy atmosphere.</p>
            </div>
          </div>
        </div>

        <div class="col-lg-6 bg-white p-4 p-md-5">
          <h2 class="mb-4 fw-bold" style="color: #3E2723;">Reserve a Table</h2>
          <form id="reservation-form" action="reservations.php" method="POST">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-semibold">Full Name</label>
                <input type="text" name="name" class="form-control p-3 bg-light border-0" placeholder="E.g. Su Latt" required>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">Phone Number</label>
                <input type="tel" name="contact" class="form-control p-3 bg-light border-0" placeholder="09xxxxxxxxx" required>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">Reservation Date</label>
                <input type="date" name="reservedDate" class="form-control p-3 bg-light border-0" required>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">Preferred Time</label>
                <input type="time" name="reservedTime" class="form-control p-3 bg-light border-0" required>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">Number of Guests</label>
                <input type="number" name="noOfGuests" class="form-control p-3 bg-light border-0" min="1" placeholder="Number of people" required>
              </div>
              <div class="col-12 mt-4">
                <button type="submit" class="btn custom-res-btn w-100 fw-bold">Confirm Reservation</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
  <footer>
    <div class="footer-container">
      <div class="footer-row">
        <div class="footer-col" id="contact">
          <h4>Contact Us</h4>
          <p>123 Galle Road, Colombo 04</p>
          <p>Email: info@mayfood.com</p>
          <p>Phone: +94 77 123 4567</p>
        </div>
        <div class="footer-col">
          <h4>Follow Us</h4>
          <div class="social-icons">
            <a href="#"><i class="fab fa-facebook-f"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
            <a href="#"><i class="fab fa-youtube"></i></a>
          </div>
        </div>
        <div class="footer-col">
          <h4>Subscribe</h4>
          <form action="#">
            <input type="email" placeholder="Your email address" required style="background-color: #f9f9f9; color: #333; margin-top: 12px;">
            <button type="submit">Subscribe</button>
          </form>
        </div>
      </div>
      <div class="footer-bottom">
        <h4>&copy; 2024  All Rights Reserved.</h4>
      </div>
    </div>
  </footer>

 <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

  <script>
      $(document).ready(function() {
          AOS.init();
          var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'))
          var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
              return new bootstrap.Dropdown(dropdownToggleEl)
          });
          $('.clients-carousel').owlCarousel({
              loop: true,
              nav: false,
              autoplay: true,
              autoplayTimeout: 5000,
              margin: 30,
              responsive: {
                  0: { items: 1 },
                  768: { items: 2 },
                  1200: { items: 2 }
              }
          });
      });
  </script>
  </script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
      AOS.init();
    </script>
    <script>
      $(document).ready(function() {
        console.log('Page is ready. Calling load_cart_item_number.');
        load_cart_item_number();

        function load_cart_item_number() {
          $.ajax({
            url: 'action.php',
            method: 'get',
            data: {
              cartItem: "cart_item"
            },
            success: function(response) {
              $("#cart-item").html(response);
            }
          });
        }
      });
    </script>
    <script>
      $('.clients-carousel').owlCarousel({
        loop: true,
        nav: false,
        autoplay: true,
        autoplayTimeout: 5000,
        animateOut: 'fadeOut',
        animateIn: 'fadeIn',
        smartSpeed: 450,
        margin: 30,
        responsive: {
          0: {
            items: 1
          },
          768: {
            items: 2
          },
          991: {
            items: 2
          },
          1200: {
            items: 2
          },
          1920: {
            items: 2
          }
        }
      });
    </script>
    <script>
      $(document).ready(function() {
          $('.top-picks-carousel').owlCarousel({
              loop: true,
              margin: 20,
              nav: true,
              dots: false,
              autoplay: true,
              autoplayTimeout: 4000,
              navText: ["<i class='fas fa-chevron-left'></i>","<i class='fas fa-chevron-right'></i>"], 
              responsive: {
                  0: { items: 1 },    
                  768: { items: 2 },
                  1000: { items: 3 }  
              }
          });
      });
      </script>
      <script>
      function showToast() {
        var toast = document.getElementById("toast");
        toast.className = "toast show";

        document.querySelector('.toast-ok').onclick = function() {
          window.location.href = 'login.php'; 
        };
        document.querySelector('.toast-close').onclick = function() {
          toast.className = toast.className.replace("show", "hide");
        };
      }
    </script>
    <script>
      document.addEventListener("DOMContentLoaded", () => {
        const elements = document.querySelectorAll('.animate-on-scroll');
        const observer = new IntersectionObserver((entries) => {
          entries.forEach(entry => {
            if (entry.isIntersecting) {
              entry.target.classList.add('reveal');
            }
          });
        }, {
          threshold: 0.1
        });

        elements.forEach(element => {
          observer.observe(element);
        });
      });
    </script>
</body>

</html>
