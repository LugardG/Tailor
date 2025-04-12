<?php
// Database connection
$conn = new mysqli("localhost", "root", "Zn9ee8@2024", "tailor");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];
    $price = trim($_POST['price']);
    $estimated_time = trim($_POST['estimated_time']);

    // Validate inputs
    if (!empty($order_id) && !empty($status) && !empty($price) && !empty($estimated_time)) {
        // Update order status, price, and estimated time in the database
        $query = "UPDATE orders SET status = ?, price = ?, estimated_time = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssi", $status, $price, $estimated_time, $order_id);

        if ($stmt->execute()) {
            echo "Order updated successfully!";
        } else {
            echo "Error updating order: " . $stmt->error;
        }
    } else {
        echo "Please fill in all fields.";
    }
}

// Close connection
$conn->close();
?>
