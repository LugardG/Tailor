<?php
// Database connection
$conn = new mysqli("localhost", "root", "Zn9ee8@2024", "tailor");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Validate and get order details securely
if (isset($_GET['order_id']) && is_numeric($_GET['order_id'])) {
    $order_id = intval($_GET['order_id']);
    
    // Use a prepared statement for security
    $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();
    $stmt->close();

    if (!$order) {
        echo "<p style='color: red; text-align: center;'>Order not found!</p>";
        exit;
    }
} else {
    echo "<p style='color: red; text-align: center;'>Invalid order request!</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - Order #<?php echo $order_id; ?></title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            padding: 20px;
        }
        .container {
            background: white;
            padding: 20px;
            max-width: 500px;
            margin: auto;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        h2 {
            color: #333;
        }
        p {
            font-size: 16px;
            margin: 10px 0;
            color: #555;
        }
        strong {
            color: #222;
        }
        .payment-box {
            background: #eee;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
        .mpesa-info {
            font-weight: bold;
            color: #28a745;
        }
        input[type="text"] {
            width: 80%;
            padding: 8px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background: #218838;
        }
        .back-link {
            display: block;
            margin-top: 15px;
            color: #007bff;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Payment for Order #<?php echo $order_id; ?></h2>
        
        <div class="payment-box">
            <p><strong>Fabric:</strong> <?php echo htmlspecialchars($order['fabric'] ?? 'N/A'); ?></p>
            <p><strong>Color:</strong> <?php echo htmlspecialchars($order['color'] ?? 'N/A'); ?></p>
            <p><strong>Measurements:</strong> <?php echo htmlspecialchars($order['measurements'] ?? 'N/A'); ?></p>
            <p><strong>Total Price:</strong> KES <?php echo number_format($order['price'] ?? 0, 2); ?></p>
            <p><strong>Required Deposit (50%):</strong> KES <?php echo number_format(($order['price'] ?? 0) / 2, 2); ?></p>
        </div>

        <h3>Pay via M-Pesa</h3>
        <p>Send <strong>KES <?php echo number_format(($order['price'] ?? 0) / 2, 2); ?></strong> to:</p>
        <p class="mpesa-info">Paybill: 247247</p>
        <p class="mpesa-info">Account Number: TAILORPRO<?php echo $order_id; ?></p>

        <form action="confirm_payment.php" method="POST">
            <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
            <label for="transaction_id">Enter M-Pesa Transaction ID:</label>
            <input type="text" name="transaction_id" placeholder="M-Pesa Code" required>
            <button type="submit">Confirm Payment</button>
        </form>

        <a href="index.html" class="back-link">← Back to Home</a>
    </div>
</body>
</html>
