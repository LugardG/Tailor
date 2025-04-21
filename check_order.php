<?php
// Database connection
$conn = new mysqli("localhost", "root", "Zn9ee8@2024", "tailor");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_GET['contact']) || empty(trim($_GET['contact']))) {
    die("<h2 style='color:red;'>Invalid request. Please enter your email or phone.</h2>");
}

$contact = trim($_GET['contact']); // Get customer input

// Search for orders based on email or phone
$query = "SELECT * FROM orders WHERE email = ? OR phone = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $contact, $contact);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("<h2 style='color:red;'>No orders found for this contact.</h2>");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Order Status</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            text-align: center;
        }
        .container {
            width: 90%;
            max-width: 800px;
            margin: 50px auto;
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #333;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
            font-size: 16px;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        td {
            color: #222;
            background-color: #fff;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .status {
            font-weight: bold;
        }
        .status-approved { color: green; }
        .status-completed { color: blue; }
        .status-pending { color: orange; }
        .no-orders {
            color: red;
            font-weight: bold;
            margin-top: 20px;
        }
        .pay-button {
            background: #4CAF50;
            color: white;
            padding: 10px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }
        .pay-button:hover { background: #45a049; }
        .back-button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            background: #444;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .back-button:hover { background: #333; }
    </style>
</head>
<body>

<div class="container">
    <h2>Your Order Status</h2>

    <table>
        <tr>
            <th>Order ID</th>
            <th>Fabric</th>
            <th>Color</th>
            <th>Measurements</th>
            <th>Price</th>
            <th>Estimated Time</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['id']) ?></td>
                <td><?= htmlspecialchars($row['fabric']) ?></td>
                <td><?= htmlspecialchars($row['color']) ?></td>
                <td><?= htmlspecialchars($row['measurements']) ?></td>
                <td><?= htmlspecialchars($row['price'] ?? 'Pending') ?></td>
                <td><?= htmlspecialchars($row['estimated_time'] ?? 'Pending') ?></td>
                <td class="status 
                    <?php 
                        if ($row['status'] == 'approved') echo 'status-approved'; 
                        elseif (strtolower($row['status']) == 'completed') echo 'status-completed'; 
                        else echo 'status-pending'; 
                    ?>">
                    <?= htmlspecialchars($row['status']) ?>
                </td>

                <td>
                    <?php if ($row['status'] == 'approved'): ?>
                        <form action="payment.php" method="GET">
                            <input type="hidden" name="order_id" value="<?= htmlspecialchars($row['id']) ?>">
                            <input type="hidden" name="price" value="<?= htmlspecialchars($row['price'] ?? 0) ?>">
                            <button type="submit" class="pay-button">Proceed to Payment</button>
                        </form>
                    <?php else: ?>
                        <span style="color: orange; font-weight: bold;">Awaiting Approval</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

    <a href="index.html" class="back-button">Go Back</a>
</div>

</body>
</html>

<?php
$conn->close();
?>
