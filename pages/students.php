<?php
require_once '../includes/db.php';
$db = getDB();

$message = '';
$message_type = '';

// Handle actions
$action = $_GET['action'] ?? '';
$edit_student = null;

// DELETE
if ($action === 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $db->query("DELETE FROM students WHERE id=$id");
    header("Location: students.php?msg=deleted");
    exit;
}

// ADD / EDIT
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id'] ?? 0);
    $student_no = $db->real_escape_string(trim($_POST['student_no']));
    $full_name = $db->real_escape_string(trim($_POST['full_name']));
    $email = $db->real_escape_string(trim($_POST['email']));
    $course = $db->real_escape_string(trim($_POST['course']));
    $year_level = intval($_POST['year_level']);
    $status = $db->real_escape_string($_POST['status']);

    if ($id > 0) {
        $db->query("UPDATE students SET student_no='$student_no', full_name='$full_name', email='$email', course='$course', year_level=$year_level, status='$status' WHERE id=$id");
        header("Location: students.php?msg=updated");
    } else {
        $db->query("INSERT INTO students (student_no, full_name, email, course, year_level, status) VALUES ('$student_no','$full_name','$email','$course',$year_level,'$status')");
        header("Location: students.php?msg=added");
    }
    exit;
}

// Load for edit
if ($action === 'edit' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $edit_student = $db->query("SELECT * FROM students WHERE id=$id")->fetch_assoc();
}

// Filter/search
$search = $db->real_escape_string($_GET['search'] ?? '');
$filter_status = $db->real_escape_string($_GET['status_filter'] ?? '');

$where = [];
if ($search) $where[] = "(student_no LIKE '%$search%' OR full_name LIKE '%$search%' OR email LIKE '%$search%')";
if ($filter_status) $where[] = "status='$filter_status'";
$where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$students = $db->query("SELECT * FROM students $where_sql ORDER BY created_at DESC");
$count = $db->query("SELECT COUNT(*) as c FROM students $where_sql")->fetch_assoc()['c'];

// Messages
$msgs = ['added' => 'Student added successfully!', 'updated' => 'Student updated!', 'deleted' => 'Student deleted.'];
if (isset($_GET['msg']) && isset($msgs[$_GET['msg']])) {
    $message = $msgs[$_GET['msg']];
    $message_type = $_GET['msg'] === 'deleted' ? 'error' : 'success';
}

require_once '../includes/header.php';

$open_modal = ($action === 'add' || $action === 'edit');
?>

<?php if ($message): ?>
<script>document.addEventListener('DOMContentLoaded', () => showToast("<?= $message ?>", "<?= $message_type ?>"));</script>
<?php endif; ?>

<?php if ($open_modal): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const formHtml = `
        <form method="POST" action="students.php">
            <input type="hidden" name="id" value="<?= $edit_student['id'] ?? 0 ?>">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Student No.</label>
                    <input class="form-control" name="student_no" required value="<?= htmlspecialchars($edit_student['student_no'] ?? '') ?>" placeholder="2024-0001">
                </div>
                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input class="form-control" name="full_name" required value="<?= htmlspecialchars($edit_student['full_name'] ?? '') ?>" placeholder="Juan Dela Cruz">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Email</label>
                <input class="form-control" type="email" name="email" required value="<?= htmlspecialchars($edit_student['email'] ?? '') ?>" placeholder="student@school.edu">
            </div>
            <div class="form-group">
                <label class="form-label">Course</label>
                <input class="form-control" name="course" required value="<?= htmlspecialchars($edit_student['course'] ?? '') ?>" placeholder="BS Computer Science">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Year Level</label>
                    <select class="form-control" name="year_level">
                        <?php for ($y=1; $y<=5; $y++): ?>
                        <option value="<?= $y ?>" <?= ($edit_student['year_level'] ?? 1) == $y ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select class="form-control" name="status">
                        <?php foreach (['Active','Graduated','Inactive','Dropped'] as $s): ?>
                        <option value="<?= $s ?>" <?= ($edit_student['status'] ?? 'Active') === $s ? 'selected' : '' ?>><?= $s ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn btn-primary"><?= $edit_student ? 'Update Student' : 'Add Student' ?></button>
            </div>
        </form>`;
    openModal('<?= $edit_student ? 'Edit Student' : 'Add New Student' ?>', formHtml);
});
</script>
<?php endif; ?>

<div class="page-header">
    <div class="page-title">
        <h1>Students</h1>
        <p><?= $count ?> student(s) found</p>
    </div>
    <a href="students.php?action=add" class="btn btn-primary">
        <svg viewBox="0 0 24 24" fill="none"><line x1="12" y1="5" x2="12" y2="19" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/><line x1="5" y1="12" x2="19" y2="12" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/></svg>
        Add Student
    </a>
</div>

<form method="GET" action="students.php">
    <div class="filter-bar">
        <div class="search-wrap">
            <svg viewBox="0 0 24 24" fill="none"><circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/><path d="m21 21-4.35-4.35" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            <input type="text" name="search" class="search-input" placeholder="Search by name, ID, or email..." value="<?= htmlspecialchars($search) ?>">
        </div>
        <select name="status_filter" class="select-filter">
            <option value="">All Status</option>
            <?php foreach (['Active','Graduated','Inactive','Dropped'] as $s): ?>
            <option value="<?= $s ?>" <?= $filter_status === $s ? 'selected' : '' ?>><?= $s ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-secondary">Filter</button>
    </div>
</form>

<div class="table-wrapper">
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Student No.</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Course</th>
                <th>Year</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $i = 1;
        while ($s = $students->fetch_assoc()): ?>
            <tr>
                <td class="td-muted"><?= $i++ ?></td>
                <td class="td-number"><?= htmlspecialchars($s['student_no']) ?></td>
                <td class="td-name"><?= htmlspecialchars($s['full_name']) ?></td>
                <td class="td-muted"><?= htmlspecialchars($s['email']) ?></td>
                <td><?= htmlspecialchars($s['course']) ?></td>
                <td><?= $s['year_level'] ?></td>
                <td><span class="badge badge-<?= strtolower($s['status']) ?>"><?= strtoupper($s['status']) ?></span></td>
                <td>
                    <div class="td-actions">
                        <a href="students.php?action=edit&id=<?= $s['id'] ?>" class="btn btn-sm btn-secondary">Edit</a>
                        <a href="grades.php?student_id=<?= $s['id'] ?>" class="btn btn-sm btn-blue">Grades</a>
                        <button onclick="confirmDelete('students.php?action=delete&id=<?= $s['id'] ?>', '<?= htmlspecialchars($s['full_name']) ?>')" class="btn btn-sm btn-danger">Delete</button>
                    </div>
                </td>
            </tr>
        <?php endwhile; ?>
        <?php if ($count === 0): ?>
        <tr><td colspan="8">
            <div class="empty-state">
                <svg class="empty-icon" viewBox="0 0 24 24" fill="none"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="2"/><circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="2"/></svg>
                <h3>No students found</h3>
                <a href="students.php?action=add">Add the first student</a>
            </div>
        </td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once '../includes/footer.php'; ?>
