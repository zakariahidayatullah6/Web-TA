<?php
require 'connectDB.php';

$users = [];
$sql = "SELECT * FROM users ORDER BY no DESC";
$stmt = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmt, $sql)) {
    error_log("Query preparation failed: " . mysqli_error($conn));
    echo '<div class="alert alert-danger">Error retrieving user data.</div>';
    exit();
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
if (!$result) {
    error_log("Query execution failed: " . mysqli_error($conn));
    echo '<div class="alert alert-danger">Error retrieving user data.</div>';
    exit();
}

while ($row = mysqli_fetch_assoc($result)) {
    $users[] = $row;
}
mysqli_stmt_close($stmt);
mysqli_close($conn);

if (empty($users)) {
    echo '<div class="alert alert-info">No users found.</div>';
    exit();
}
?>

<div class="table-responsive-sm" style="max-height: 870px; background-color: rgb(43, 73, 165); border-radius: 0 0 1rem 1rem;">
    <table class="table">
        <thead class="table-primary">
            <tr>
                <th>Card UID</th>
                <th>Name</th>
                <th>Gender</th>
                <th>S.No</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody class="table-secondary">
            <?php foreach ($users as $row): ?>
                <tr>
                    <td>
                        <?php if ($row['card_select'] == 1): ?>
                            <span><i class="fas fa-check" title="The selected UID"></i></span>
                        <?php endif; ?>
                        <form>
                            <button type="button" class="select_btn" id="<?php echo htmlspecialchars($row['card_uid']); ?>" title="Select this UID">
                                <?php echo htmlspecialchars($row['card_uid']); ?>
                            </button>
                        </form>
                    </td>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td><?php echo htmlspecialchars($row['gender']); ?></td>
                    <td><?php echo htmlspecialchars($row['serialnumber']); ?></td>
                    <td><?php echo htmlspecialchars($row['user_date']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>