<?php

// ============================================================================
// PHP Setups
// ============================================================================

date_default_timezone_set('Asia/Kuala_Lumpur');
// start new session - state management technique
session_start(); // to retain value, http by default cannot remember anything so is stateless

// ============================================================================
// General Page Functions
// ============================================================================

// Is GET request?
function is_get() {
    return $_SERVER['REQUEST_METHOD'] == 'GET';
}

// Is POST request?
function is_post() {
    return $_SERVER['REQUEST_METHOD'] == 'POST';
}

// Obtain GET parameter
function get($key, $value = null) {
    $value = $_GET[$key] ?? $value;
    return is_array($value) ? array_map('trim', $value) : trim($value);
}

// Obtain POST parameter
function post($key, $value = null) {
    $value = $_POST[$key] ?? $value;
    return is_array($value) ? array_map('trim', $value) : trim($value);
}

// Obtain REQUEST (GET and POST) parameter
function req($key, $value = null) {
    $value = $_REQUEST[$key] ?? $value;
    return is_array($value) ? array_map('trim', $value) : trim($value);
}

// Redirect to URL
function redirect($url = null) {
    // if URL is not given, set the URL to the current URL location
    $url ??= $_SERVER['REQUEST_URI'];
    header("Location:$url"); // rediredct the page without user involvemnet
    exit();
}

// Set or get temporary session variable
function temp($key, $value = null) {
    if($value !== null){ // with value
        // store the value into a session variable
        $_SESSION["temp$key"]= $value;
    }else{ // no value
        // retreive from session
        $value = $_SESSION["temp$key"] ?? null;
        unset($_SESSION["temp$key"]); // remove session
        return $value;
    }
}

// ============================================================================
// HTML Helpers
// ============================================================================

// Encode HTML special characters
// eg: >, convert ">" to &gt(greater than);
function encode($value) {
    return htmlentities($value);
}

// Generate <input type='text'>
// ensure reusable, can use it again n again
function html_text($key, $attr = '') {
    // if there is a value entered by the user into the textbox, it will retrieve input text based $key(textbox's name)
    $value = encode($GLOBALS[$key] ?? '');
    echo "<input type = 'text' id = '$key' name = '$key' value = '$value' $attr>"; // echo = output
}

// Generate <input type='radio'> list
function html_radios($key, $items, $br = false) {
    $value = encode($GLOBALS[$key] ?? '');
    echo '<div>';
    foreach ($items as $id => $text) {
        $state = $id == $value ? 'checked' : '';
        echo "<label><input type='radio' id='{$key}_$id' name='$key' value='$id' $state>$text</label>";
        if ($br) {
            echo '<br>';
        }
    }
    echo '</div>';
}

// Generate <select> - drop down list
function html_select($key, $items, $default = '- Select One -', $attr = '') {
    $value = encode($GLOBALS[$key] ?? '');
    echo "<select id='$key' name='$key' $attr>";
    if ($default !== null) {
        echo "<option value=''>$default</option>";
    }
    foreach ($items as $id => $text) {
        $state = $id == $value ? 'selected' : '';
        echo "<option value='$id' $state>$text</option>";
    }
    echo '</select>';
}

// ============================================================================
// Error Handlings
// ============================================================================

// Global error array
$_err = [];

// Generate <span class='err'>
function err($key) {
    // access the global array
    global $_err;
    // why need a $key? because we may have to diaplay different message for different input element
    if($_err[$key] ?? false){
        // with error
        echo "<span class = 'err'>$_err[$key]</span>";
    }else{
        // with NO error
        echo "<span></span>";
    }
}

// ============================================================================
// Global Constants and Variables
// ============================================================================

$_genders = [
    'F' => 'Female',
    'M' => 'Male',
];

$_programs = [
    'RDS' => 'Data Science',
    'REI' => 'Enterprise Information Systems',
    'RIS' => 'Information Security',
    'RSD' => 'Software Systems Development',
    'RST' => 'Interactive Software Technology',
    'RSW' => 'Software Engineering',
];