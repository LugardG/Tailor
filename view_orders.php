<?php
// Database connection
$conn = new mysqli("localhost", "root", "Zn9ee8@2024", "tailor");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch orders
$sql = "SELECT * FROM orders ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tailor Orders</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; padding: 20px; background-color: #f9f9f9; }
        h2 { text-align: center; color: #333; }
        table { width: 100%; border-collapse: collapse; background: white; box-shadow: 0px 0px 10px rgba(0,0,0,0.1); }
        th, td { border: 1px solid black; padding: 10px; text-align: left; }
        th { background-color: #4CAF50; color: white; }
        img { width: 100px; height: auto; border-radius: 5px; }
        form { display: flex; flex-direction: column; gap: 5px; }
        input, select { padding: 5px; width: 100%; }
        button { background: #28a745; color: white; border: none; padding: 5px; cursor: pointer; }
        button:hover { background: #218838; }
        .status-pending { color: orange; font-weight: bold; }
        .status-approved { color: green; font-weight: bold; }
        .status-completed { color: blue; font-weight: bold; }
    </style>
</head>
<body>

    <h2>Customer Orders</h2>
    
    <table>
        <tr>
            <th>ID</th>
            <th>Customer</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Fabric</th>
            <th>Color</th>
            <th>Measurements</th>
            <th>Reference Image</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['id']) ?></td>
                <td><?= htmlspecialchars($row['customer_name']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['phone']) ?></td>
                <td><?= htmlspecialchars($row['fabric']) ?></td>
                <td><?= htmlspecialchars($row['color']) ?></td>
                <td><?= htmlspecialchars($row['measurements']) ?></td>
                <td>
                    <?php if (!empty($row['reference_image'])): ?>
                        <img src="uploads/<?= htmlspecialchars(basename($row['reference_image'])) ?>" alt="Reference Image">
                    <?php else: ?>
                        <span style="color: gray;">Awaiting Upload</span>
                    <?php endif; ?>
                </td>
                <!-- Status column showing text only -->
                <td class="
                    <?php 
                        if ($row['status'] == 'Approved') echo 'status-approved'; 
                        elseif ($row['status'] == 'Completed') echo 'status-completed'; 
                        else echo 'status-pending'; 
                    ?>">
                    <?= htmlspecialchars($row['status']) ?>
                </td>
                <!-- Action column containing the form -->
                <td>
                    <form action="update_order.php" method="POST">
                        <input type="hidden" name="order_id" value="<?= htmlspecialchars($row['id']) ?>">
                        <select name="status">
                            <option value="Pending" <?= ($row['status'] == 'Pending') ? 'selected' : '' ?>>Pending</option>
                            <option value="Approved" <?= ($row['status'] == 'Approved') ? 'selected' : '' ?>>Approved</option>
                            <option value="Completed" <?= ($row['status'] == 'Completed') ? 'selected' : '' ?>>Completed</option>
                        </select>
                        <input type="text" name="price" placeholder="Enter Price" value="<?= htmlspecialchars($row['price'] ?? '') ?>" required>
                        <input type="text" name="estimated_time" placeholder="Enter Time" value="<?= htmlspecialchars($row['estimated_time'] ?? '') ?>" required>
                        <button type="submit" style="background: #28a745; color: white; padding: 5px; border: none; cursor: pointer;">Update</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

</body>
</html>

<?php $conn->close(); ?>
