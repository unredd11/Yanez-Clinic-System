  <?php
  session_start();

  //if the user is logged in
  $is_logged_in = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
  $username = $_SESSION['username'] ?? '';
  ?>

  <!DOCTYPE html>
  <html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yañez X-Ray Medical Clinic and Laboratory</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/yanezstyle.css">
  </head>
  <body>

 <?php include 'header.php'; ?>


    <section class="hero">
      <h2>Your Trusted Clinic in iligan City</h2>
      <p>The diagnostic laboratory of choice for quality medical services.</p>
      <a href="book_appointment.php" class="btn">Book Appointment</a>
    </section>

    <section class="section">
      <section id="services" class="section">
      <h3>Our Services</h3>
      <div class="services">
        <div class="service">
          <h4>X-ray & Diagnostics</h4>
          <p>We offer diagnostic procedures such as X-Ray and ECG (12 Leads) to help detect and monitor various medical conditions. Our X-Ray services cover a wide range of examinations including chest, skull, extremities, pelvis, spine, and more.</p>
        </div>
        <div class="service">
          <h4>Laboratory Testing</h4>
          <p>Our fully equipped laboratory provides comprehensive testing in Blood Chemistry, Hematology, Serology, and Clinical Microscopy. These include essential tests such as lipid profile, fasting blood sugar, CBC, blood typing, pregnancy test, HIV rapid test, urinalysis, fecalysis, and many others.</p>
        </div>
        <div class="service">
          <h4>Physical Examination</h4>
          <p>We provide medical and physical examinations for patients of all ages. Patients can walk in and request specific services as needed.</p>
        </div>
      </div>
    </section>
    </section>

    <section id="hours" class="section">
      <div class="hours">
        <h3>Operating Hours</h3>
        <ul>
          <li>Monday - Friday: 7:00 AM - 4:30 PM</li>
          <li>Saturday: 7:00 AM - 12:00 PM</li>
          <li>Sunday: Closed</li>
        </ul>
      </div>
    </section>

    <section id="about" class="section about">
      <h3>About Us</h3>
      <p>Yañez X-ray and Medical Clinic strives to enhance clinic standards and offer an efficient patient experience by employing a highly efficient appointment scheduling system. In comparison to other local institutions, Yañez X-ray and Medical Clinic in Iligan City occupies an enormous area. Through an integrated network of laboratories, the clinic provides sustainable, affordable, and high-quality health laboratory services. </p>
      <p>We combine modern technology with compassionate care to deliver the best medical services.</p>
    </section>

    <section class = "section about loc.">
      <P>We are located at 66JM+CXW, Sabayle, Iligan City, Lanao del Norte</P>
      <div class = "location-map"></div>
      <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3948.7176976996643!2d124.23237877656835!3d8.231117400964573!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x325575dd8dc668ff%3A0x69a44f168e57875b!2sYan%C3%B1ez%20X-Ray%20Medical%20Clinic%20%26%20Laboratory!5e0!3m2!1sen!2sph!4v1756728768755!5m2!1sen!2sph" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>

      <div class="contact">
          <P>For inquiries, please reach out to us:</P>
        <h3>Visit Us and Contact Us</h3>
        <div class="contact-info">
          <div class="contact-item">Sabayle St., Poblacion, Iligan City.</div>
          <div class="contact-item">Tel: 222-8130</div>
        </div>
      </div>
    </section>

  <?php include "footer.php";?>
  
  </body>
  </html>