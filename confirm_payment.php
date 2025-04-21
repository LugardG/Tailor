<?php
// Database connection
$conn = new mysqli("localhost", "root", "Zn9ee8@2024", "tailor");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = $_POST['order_id'];
    $transaction_id = $_POST['transaction_id'];

    // Update order to half-paid
    $query = "UPDATE orders SET payment_status = 'half-paid' WHERE id = $order_id";
    if (mysqli_query($conn, $query)) {
        echo "<p>Payment confirmed! Your order has been sent to the tailor.</p>";
    } else {
        echo "<p>Error updating payment status.</p>";
    }
}
?>
