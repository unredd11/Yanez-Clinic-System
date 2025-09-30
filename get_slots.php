<?php
require 'connect.php';

if (!empty($_GET['date'])) {
    $date = mysqli_real_escape_string($conn, $_GET['date']);
    $bookedSlots = [];

    $result = mysqli_query($conn, "SELECT appointment_time FROM appointment WHERE appointment_date = '$date'");
    while ($row = mysqli_fetch_assoc($result)) {
        $bookedSlots[] = $row['appointment_time'];
    }

    function generateTimeSlots($startHour, $endHour, $interval, $bookedSlots) {
        $slots = "";
        for ($hour = $startHour; $hour < $endHour; $hour++) {
            if ($hour == 12) continue; // skip lunch
            for ($min = 0; $min < 60; $min += $interval) {
                $time = sprintf("%02d:%02d:00", $hour, $min);
                $timeDisplay = date("g:i A", strtotime($time));

                if (in_array($time, $bookedSlots)) {
                    $slots .= "<button type='button' class='time-slot booked' disabled>$timeDisplay (Booked)</button>";
                } else {
                    $slots .= "<button type='button' class='time-slot' onclick=\"selectTime('$time', this)\">$timeDisplay</button>";
                }
            }
        }
        return $slots;
    }

    echo generateTimeSlots(8, 17, 30, $bookedSlots);
}
?>
