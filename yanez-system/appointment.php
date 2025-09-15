<?php
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'connect.php'; // DB connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Debug: Show what data we received
    echo "<!-- Debug: POST data received -->";
    
    // Validate required fields
    if (empty($_POST['patientName']) || empty($_POST['patientPhone']) || empty($_POST['patientEmail']) || 
        empty($_POST['selectedDate']) || empty($_POST['selectedTime']) || empty($_POST['selectedService'])) {
        echo "<script>alert('Please fill in all required fields and make your selections.'); window.history.back();</script>";
        exit();
    }

    $name    = mysqli_real_escape_string($conn, trim($_POST['patientName']));
    $phone   = mysqli_real_escape_string($conn, trim($_POST['patientPhone']));
    $email   = mysqli_real_escape_string($conn, trim($_POST['patientEmail']));
    $notes   = isset($_POST['notes']) ? mysqli_real_escape_string($conn, trim($_POST['notes'])) : '';
    $date    = mysqli_real_escape_string($conn, $_POST['selectedDate']);
    $time    = mysqli_real_escape_string($conn, $_POST['selectedTime']);
    $service = mysqli_real_escape_string($conn, $_POST['selectedService']);

    // Convert 12-hour time format to 24-hour format for database storage
    $time_24hr = date("H:i:s", strtotime($time));

    // 1. Check if time slot is already booked
    $check_slot = "SELECT appointment_id FROM appointment 
                   WHERE appointment_date = '$date' 
                   AND appointment_time = '$time_24hr'";
    $slot_result = mysqli_query($conn, $check_slot);

    if (!$slot_result) {
        echo "<script>alert('Database error occurred: " . mysqli_error($conn) . "'); window.history.back();</script>";
        exit();
    }

    if (mysqli_num_rows($slot_result) > 0) {
        echo "<script>alert('Sorry, this time slot is already booked. Please choose another time.'); window.history.back();</script>";
        exit();
    }

    // 2. Check if patient already exists or create new
    $sql_patient = "SELECT patient_id FROM patient WHERE email='$email'";
    $result = mysqli_query($conn, $sql_patient);

    if (!$result) {
        echo "<script>alert('Database error occurred: " . mysqli_error($conn) . "'); window.history.back();</script>";
        exit();
    }

    if (mysqli_num_rows($result) > 0) {
        // Patient exists, get their ID and update their info
        $row = mysqli_fetch_assoc($result);
        $patient_id = $row['patient_id'];
        
        // Update patient info in case it changed
        $name_parts = explode(' ', $name, 2);
        $first_name = $name_parts[0];
        $last_name = isset($name_parts[1]) ? $name_parts[1] : '';
        
        $update_patient = "UPDATE patient SET 
                          first_name = '$first_name',
                          last_name = '$last_name',
                          phone_number = '$phone' 
                          WHERE patient_id = '$patient_id'";
        mysqli_query($conn, $update_patient);
    } else {
        // Create new patient - split full name into first and last name
        $name_parts = explode(' ', $name, 2);
        $first_name = $name_parts[0];
        $last_name = isset($name_parts[1]) ? $name_parts[1] : '';
        
        // Generate default username and password (optional)
        $username = strtolower($first_name . $last_name . rand(100, 999));
        $password = password_hash('defaultpass123', PASSWORD_DEFAULT); // Hash the password
        $birthdate = '1990-01-01'; // Default birthdate
        
        $insert_patient = "INSERT INTO patient (first_name, last_name, email, phone_number, birthdate, username, password) 
                           VALUES ('$first_name', '$last_name', '$email', '$phone', '$birthdate', '$username', '$password')";
        
        if (!mysqli_query($conn, $insert_patient)) {
            echo "<script>alert('Error creating patient record: " . mysqli_error($conn) . "'); window.history.back();</script>";
            exit();
        }
        $patient_id = mysqli_insert_id($conn);
    }

    // 3. Get a valid doctor (make sure doctor exists)
    $doctor_check = "SELECT doctor_id FROM doctor LIMIT 1";
    $doctor_result = mysqli_query($conn, $doctor_check);
    
    if (mysqli_num_rows($doctor_result) == 0) {
        // Create a default doctor if none exists
        $insert_doctor = "INSERT INTO doctor (name, specialization, email, phone_number, username, password) 
                         VALUES ('Dr. Default', 'General Practice', 'doctor@clinic.com', '123-456-7890', 'admin', '" . password_hash('admin123', PASSWORD_DEFAULT) . "')";
        mysqli_query($conn, $insert_doctor);
        $doctor_id = mysqli_insert_id($conn);
    } else {
        $doctor_row = mysqli_fetch_assoc($doctor_result);
        $doctor_id = $doctor_row['doctor_id'];
    }

    // 4. Insert appointment
    $insert_appointment = "INSERT INTO appointment 
        (patient_id, doctor_id, service, appointment_date, appointment_time, status, appointment_details) 
        VALUES ('$patient_id', '$doctor_id', '$service', '$date', '$time_24hr', 'Pending', '$notes')";

    if (mysqli_query($conn, $insert_appointment)) {
        $appointment_id = mysqli_insert_id($conn);
        echo "<script>
            alert('Booking Request Sent! Your appointment has been successfully recorded. Appointment ID: #$appointment_id');
            window.location.href = 'appointment.php';
        </script>";
        exit();
    } else {
        echo "<script>alert('Error booking appointment: " . mysqli_error($conn) . "'); window.history.back();</script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Book Appointment - Yañez X-Ray Medical Clinic</title>
  <link rel="stylesheet" href="yanezstyle.css" />
</head>
<body>
  <header>
   <?php include 'header.php'; ?>
  </header>
       <div class = "appointment-wrapper">
      <div class = "appointment-header">
        <h2>Appointment Area</h2>
      </div>
    </div>
  <main class="main-container">
  <!-- Calendar -->
  <div class="calendar-container">
    <div class="calendar-header">
      <button onclick="changeMonth(-1)">‹</button>
      <div id="monthYear"></div>
      <button onclick="changeMonth(1)">›</button>
    </div>
    <div class="calendar-grid" id="calendar"></div>
  </div>

<!-- Time Slots -->
    <div class="time-slots-container">
      <h2>Available Time Slots</h2>
      <div class="time-slots-flex" id="timeslots"></div>
    </div>

  <!-- Service Selection -->
  <div class="service-selection">
    <h2>Select Services</h2>
    <div class="service-grid">
      <div class="service-card" onclick="selectService('X-Ray', this)">
        <div class="service-icon">🔬</div>
        <div>
          <div style="font-weight: 600;">X-Ray</div>
          <div style="font-size: 0.9em; opacity: 0.7;">Diagnostic Imaging</div>
        </div>
      </div>
      <div class="service-card" onclick="selectService('Laboratory Testing', this)">
        <div class="service-icon">🧪</div>
        <div>
          <div style="font-weight: 600;">Laboratory Testing</div>
          <div style="font-size: 0.9em; opacity: 0.7;">Blood & Urine Tests</div>
        </div>
      </div>
      <div class="service-card" onclick="selectService('Medical Consultation', this)">
        <div class="service-icon">👨‍⚕️</div>
        <div>
          <div style="font-weight: 600;">Medical Consultation</div>
          <div style="font-size: 0.9em; opacity: 0.7;">General Check-up</div>
        </div>
      </div>
    </div>
  </div>

  <!-- Booking Form -->
  <form class="booking-form" action="book_appointment.php" method="POST" onsubmit="return validateForm()">
    <div class="appointment-form">
      <label for="patientName">Full Name *</label>
      <input type="text" id="patientName" name="patientName" required>
    </div>
    <div class="appointment-form">
      <label for="patientPhone">Phone Number *</label>
      <input type="tel" id="patientPhone" name="patientPhone" required>
    </div>
    <div class="appointment-form">
      <label for="patientEmail">Email *</label>
      <input type="email" id="patientEmail" name="patientEmail" required>
    </div>
    <div class="appointment-form">
      <label for="notes">Additional Notes</label>
      <textarea id="notes" name="notes" rows="3" placeholder="Any special requirements or notes..."></textarea>
    </div>

    <!-- Hidden fields to store JS-selected values -->
    <input type="hidden" name="selectedDate" id="selectedDate">
    <input type="hidden" name="selectedTime" id="selectedTime">
    <input type="hidden" name="selectedService" id="selectedService">

    <button class="btn btn-primary" type="submit" id="bookBtn" disabled>
      Book Appointment
    </button>
  </form>

</main>
  <?php include "footer.php" ?>

<script>
// Calendar logic
let today = new Date();
let currentMonth = today.getMonth();
let currentYear = today.getFullYear();

const monthYearEl = document.getElementById("monthYear");
const calendarEl = document.getElementById("calendar");
const timeslotsEl = document.getElementById("timeslots");
const selectedDateInput = document.getElementById("selectedDate");
const selectedTimeInput = document.getElementById("selectedTime");
const selectedServiceInput = document.getElementById("selectedService");
const bookBtn = document.getElementById("bookBtn");

let selectedDate = null;
let selectedTime = null;
let selectedService = null;

const months = [
  "January","February","March","April","May","June",
  "July","August","September","October","November","December"
];

function renderCalendar(month, year) {
  calendarEl.innerHTML = "";
  monthYearEl.textContent = `${months[month]} ${year}`;

  // Day names
  ["Sun","Mon","Tue","Wed","Thu","Fri","Sat"].forEach(d => {
    const dayEl = document.createElement('div');
    dayEl.textContent = d;
    dayEl.classList.add('day-name');
    calendarEl.appendChild(dayEl);
  });

  const firstDay = new Date(year, month, 1).getDay();
  const daysInMonth = new Date(year, month + 1, 0).getDate();

  // Empty slots before first day
  for (let i = 0; i < firstDay; i++) {
    calendarEl.appendChild(document.createElement('div'));
  }

  // Days
  for (let day = 1; day <= daysInMonth; day++) {
    const dayEl = document.createElement('div');
    dayEl.textContent = day;
    dayEl.classList.add('calendar-day');
    
    // Disable past dates
    const currentDate = new Date(year, month, day);
    const todayDate = new Date();
    todayDate.setHours(0, 0, 0, 0);
    
    if (currentDate < todayDate) {
      dayEl.classList.add('disabled');
    } else {
      dayEl.addEventListener('click', () => selectDate(day, month, year, dayEl));
    }
    
    calendarEl.appendChild(dayEl);
  }
}

function changeMonth(delta) {
  currentMonth += delta;
  if (currentMonth < 0) {
    currentMonth = 11;
    currentYear--;
  } else if (currentMonth > 11) {
    currentMonth = 0;
    currentYear++;
  }
  renderCalendar(currentMonth, currentYear);
}

function selectDate(day, month, year, dayEl) {
  if (dayEl.classList.contains('disabled')) {
    return;
  }
  
  selectedDate = `${year}-${String(month+1).padStart(2,'0')}-${String(day).padStart(2,'0')}`;
  selectedDateInput.value = selectedDate;

  document.querySelectorAll('.calendar-day').forEach(el => el.classList.remove('selected'));
  dayEl.classList.add('selected');

  generateTimeSlots(8, 17, 30);
  selectedTime = null;
  selectedTimeInput.value = '';
  updateBookBtn();
}

function generateTimeSlots(startHour, endHour, intervalMinutes) {
  timeslotsEl.innerHTML = '';

  for (let hour = startHour; hour < endHour; hour++) {
    if (hour === 12) continue; // skip lunch break

    for (let min = 0; min < 60; min += intervalMinutes) {
      const dateObj = new Date();
      dateObj.setHours(hour);
      dateObj.setMinutes(min);

      const timeStr = dateObj.toLocaleTimeString([], {
        hour: 'numeric',
        minute: '2-digit',
        hour12: true
      });

      const slotEl = document.createElement('button');
      slotEl.type = 'button';
      slotEl.className = 'time-slot';
      slotEl.textContent = timeStr;
      slotEl.onclick = () => selectTime(timeStr, slotEl);
      timeslotsEl.appendChild(slotEl);
    }
  }
}

function selectTime(timeStr, slotEl) {
  if (slotEl.classList.contains('selected')) {
    slotEl.classList.remove('selected');
    selectedTime = null;
    selectedTimeInput.value = '';
  } else {
    document.querySelectorAll('.time-slot').forEach(el => el.classList.remove('selected'));
    slotEl.classList.add('selected');
    selectedTime = timeStr;
    selectedTimeInput.value = selectedTime;
  }
  updateBookBtn();
}

function selectService(service, cardEl) {
  selectedService = service;
  selectedServiceInput.value = service;
  document.querySelectorAll('.service-card').forEach(el => el.classList.remove('selected'));
  cardEl.classList.add('selected');
  updateBookBtn();
}

function updateBookBtn() {
  if (selectedDate && selectedTime && selectedService) {
    bookBtn.disabled = false;
  } else {
    bookBtn.disabled = true;
  }
}

function validateForm() {
  console.log('Validating form...', {
    selectedDate: selectedDate,
    selectedTime: selectedTime,
    selectedService: selectedService
  });
  
  if (!selectedDate || !selectedTime || !selectedService) {
    alert('Please select a date, time, and service before booking.');
    return false;
  }
  
  const name = document.getElementById('patientName').value.trim();
  const phone = document.getElementById('patientPhone').value.trim();
  const email = document.getElementById('patientEmail').value.trim();
  
  if (!name || !phone || !email) {
    alert('Please fill in all required fields.');
    return false;
  }
  
  return true;
}

// Initial render
renderCalendar(currentMonth, currentYear);
</script>

</body>
</html>