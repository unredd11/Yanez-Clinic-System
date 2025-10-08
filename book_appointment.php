<?php
session_start();
require 'connect.php';

// Check if patient is logged in
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient_id       = $_SESSION['patient_id']; // patient id from session
    $service          = $_POST['selectedService'];
    $appointment_date = $_POST['selectedDate'];
    $appointment_time = $_POST['selectedTime'];
    $status           = "Pending"; 
    $details          = $_POST['notes'];

    $stmt = $conn->prepare("INSERT INTO appointment 
        (patient_id, service, appointment_date, appointment_time, status, appointment_details) 
        VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssss", $patient_id, $service, $appointment_date, $appointment_time, $status, $details);

    if ($stmt->execute()) {
        echo "<script>alert('Appointment request submitted successfully!'); window.location='yanezindex.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
  <title>Book Appointment - Yañez X-Ray Medical Clinic</title>
  <link rel="stylesheet" href="css/yanezstyle.css"/>
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
      <div class="service-card" onclick="selectService('Physical Examination', this)">
        <div class="service-icon">👨‍⚕️</div>
        <div>
          <div style="font-weight: 600;">Physical Examination</div>
          <div style="font-size: 0.9em; opacity: 0.7;">Physical Check-up</div>
        </div>
      </div>
    </div>
  </div>
</main>

  <!-- Booking Form -->
<form class="booking-form" action="book_appointment.php" method="POST" onsubmit="return validateForm()">
  <div class="appointment-form">
    <label for="notes">Additional details</label>
    <textarea id="notes" name="notes" rows="3" placeholder="Please provide any additional details about your condition or special requests"></textarea>
  </div>

  <!-- Hidden fields to store JS-selected values -->
  <input type="hidden" name="selectedDate" id="selectedDate">
  <input type="hidden" name="selectedTime" id="selectedTime">
  <input type="hidden" name="selectedService" id="selectedService">

  <button class="btn btn-primary" type="submit" id="bookBtn" disabled>
    Book Appointment
  </button>
    <h2 style="font-weight: bold; color: #ffffff;">Important Notice</h2>
    <p style="color: #ffffff;">Your details will be recorded after registering an account and used for booking purposes. Please complete all required fields and double-check your selected date, time, and service before confirming your appointment.</p>
</form>

  </div>
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

// Select date
function selectDate(day, month, year, dayEl) {
  if (dayEl.classList.contains('disabled')) {
    return;
  }
  
  selectedDate = `${year}-${String(month+1).padStart(2,'0')}-${String(day).padStart(2,'0')}`;
  selectedDateInput.value = selectedDate;

  // Highlight selected day
  document.querySelectorAll('.calendar-day').forEach(el => el.classList.remove('selected'));
  dayEl.classList.add('selected');

  // Generate time slots for selected date
  generateTimeSlots(8, 17, 30);
  selectedTime = null;
  selectedTimeInput.value = '';
  updateBookBtn();
}

// Generate time slots
function generateTimeSlots(startHour, endHour, intervalMinutes) {
  timeslotsEl.innerHTML = '';

  for (let hour = startHour; hour < endHour; hour++) {
    if (hour === 12) continue; // skip lunch break

    for (let min = 0; min < 60; min += intervalMinutes) {
      const dateObj = new Date();
      dateObj.setHours(hour);
      dateObj.setMinutes(min);

      // Format to 12-hour time with AM/PM
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

// Select time slot
function selectTime(timeStr, slotEl) {
  // If this slot is already selected, unselect it
  if (slotEl.classList.contains('selected')) {
    slotEl.classList.remove('selected');
    selectedTime = null;
    selectedTimeInput.value = '';
  } else {
    // Remove selection from all slots
    document.querySelectorAll('.time-slot').forEach(el => el.classList.remove('selected'));
    // Select the clicked slot
    slotEl.classList.add('selected');
    selectedTime = timeStr;
    selectedTimeInput.value = selectedTime;
  }
  updateBookBtn();
}

// Service selection
function selectService(service, cardEl) {
  selectedService = service;
  selectedServiceInput.value = service;
  document.querySelectorAll('.service-card').forEach(el => el.classList.remove('selected'));
  cardEl.classList.add('selected');
  updateBookBtn();
}

// Enable Book button only if all required selections are made
function updateBookBtn() {
  if (selectedDate && selectedTime && selectedService) {
    bookBtn.disabled = false;
  } else {
    bookBtn.disabled = true;
  }
}

// Validate form before submission
function validateForm() {
  if (!selectedDate || !selectedTime || !selectedService) {
    alert('Please select a date, time, and service before booking.');
    return false;
  }

  const notes = document.getElementById('notes').value.trim();
  if (!notes) {
  alert('Please enter additional notes.');
  return false;
  }

  return true;
}


// Initial render
renderCalendar(currentMonth, currentYear);
</script>

</body>
</html>