<?php
// Database connection
$conn = new mysqli("localhost", "root", "Zn9ee8@2024", "tailor");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get order details
$customer_name = $_POST['customer_name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$fabric = $_POST['fabric'];
$color = $_POST['color'];
$measurements = $_POST['measurements'];
$status = "Pending"; // Default status

// Handle image upload
$target_dir = "uploads/";
$reference_image = "";

if (!empty($_FILES["reference_image"]["name"])) {
    $target_file = $target_dir . basename($_FILES["reference_image"]["name"]);
    if (move_uploaded_file($_FILES["reference_image"]["tmp_name"], $target_file)) {
        $reference_image = $target_file;
    }
}

// ✅ Insert customer details (if not already in the database)
$check_customer = $conn->prepare("SELECT id FROM customers WHERE email = ?");
$check_customer->bind_param("s", $email);
$check_customer->execute();
$check_customer->store_result();

if ($check_customer->num_rows == 0) {
    $insert_customer = $conn->prepare("INSERT INTO customers (name, email, phone) VALUES (?, ?, ?)");
    $insert_customer->bind_param("sss", $customer_name, $email, $phone);
    $insert_customer->execute();
}

// ✅ Insert order details into the orders table
$insert_order = $conn->prepare("INSERT INTO orders (customer_name, email, phone, fabric, color, measurements, reference_image, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$insert_order->bind_param("ssssssss", $customer_name, $email, $phone, $fabric, $color, $measurements, $reference_image, $status);

if ($insert_order->execute()) {
    echo "Order placed successfully!";
} else {
    echo "Error: " . $conn->error;
}

// Close connections
$insert_order->close();
$conn->close();
?>
