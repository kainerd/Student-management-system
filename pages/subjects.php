<?php
require_once '../includes/db.php';
$db = getDB();

$action = $_GET['action'] ?? '';
$edit_subject = null;

// DELETE
if ($action === 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $db->query("DELETE FROM subjects WHERE id=$id");
    header("Location: subjects.php?msg=deleted");
    exit;
}

// ADD / EDIT
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id'] ?? 0);
    $code = $db->real_escape_string(trim($_POST['code']));
    $title = $db->real_escape_string(trim($_POST['title']));
    $units = intval($_POST['units']);
    $department = $db->real_escape_string(trim($_POST['department']));

    if ($id > 0) {
        $db->query("UPDATE subjects SET code='$code', title='$title', units=$units, department='$department' WHERE id=$id");
        header("Location: subjects.php?msg=updated");
    } else {
        $db->query("INSERT INTO subjects (code, title, units, department) VALUES ('$code','$title',$units,'$department')");
        header("Location: subjects.php?msg=added");
    }
    exit;
}

if ($action === 'edit' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $edit_subject = $db->query("SELECT * FROM subjects WHERE id=$id")->fetch_assoc();
}

$search = $db->real_escape_string($_GET['search'] ?? '');
$where = $search ? "WHERE (code LIKE '%$search%' OR title LIKE '%$search%' OR department LIKE '%$search%')" : '';
$subjects = $db->query("SELECT s.*, (SELECT COUNT(*) FROM grades g WHERE g.subject_id = s.id) as grade_records FROM subjects s $where ORDER BY code");
$count = $db->query("SELECT COUNT(*) as c FROM subjects $where")->fetch_assoc()['c'];

$msgs = ['added' => 'Subject added!', 'updated' => 'Subject updated!', 'deleted' => 'Subject deleted.'];
$message = isset($_GET['msg']) ? ($msgs[$_GET['msg']] ?? '') : '';

require_once '../includes/header.php';
$open_modal = ($action === 'add' || $action === 'edit');
?>

<?php if ($message): ?>
<script>document.addEventListener('DOMContentLoaded', () => showToast("<?= $message ?>", "<?= strpos($message,'delet') !== false ? 'error' : 'success' ?>"));</script>
<?php endif; ?>

<?php if ($open_modal): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const formHtml = `
        <form method="POST" action="subjects.php">
            <input type="hidden" name="id" value="<?= $edit_subject['id'] ?? 0 ?>">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Subject Code</label>
                    <input class="form-control" name="code" required value="<?= htmlspecialchars($edit_subject['code'] ?? '') ?>" placeholder="CS101">
                </div>
                <div class="form-group">
                    <label class="form-label">Units</label>
                    <input class="form-control" type="number" name="units" min="1" max="6" required value="<?= $edit_subject['units'] ?? 3 ?>">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Subject Title</label>
                <input class="form-control" name="title" required value="<?= htmlspecialchars($edit_subject['title'] ?? '') ?>" placeholder="Introduction to Computing">
            </div>
            <div class="form-group">
                <label class="form-label">Department</label>
                <input class="form-control" name="department" required value="<?= htmlspecialchars($edit_subject['department'] ?? '') ?>" placeholder="CS Dept">
            </div>
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn btn-primary"><?= $edit_subject ? 'Update Subject' : 'Add Subject' ?></button>
            </div>
        </form>`;
    openModal('<?= $edit_subject ? 'Edit Subject' : 'Add New Subject' ?>', formHtml);
});
</script>
<?php endif; ?>

<div class="page-header">
    <div class="page-title">
        <h1>Subjects</h1>
        <p><?= $count ?> subject(s) in the system</p>
    </div>
    <a href="subjects.php?action=add" class="btn btn-primary">
        <svg viewBox="0 0 24 24" fill="none"><line x1="12" y1="5" x2="12" y2="19" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/><line x1="5" y1="12" x2="19" y2="12" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/></svg>
        Add Subject
    </a>
</div>

<form method="GET" action="subjects.php" style="margin-bottom:20px">
    <div class="filter-bar">
        <div class="search-wrap">
            <svg viewBox="0 0 24 24" fill="none"><circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/><path d="m21 21-4.35-4.35" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            <input type="text" name="search" class="search-input" placeholder="Search subjects by code, title, or department..." value="<?= htmlspecialchars($search) ?>">
        </div>
        <button type="submit" class="btn btn-secondary">Search</button>
    </div>
</form>

<div class="table-wrapper">
    <table>
        <thead>
            <tr>
                <th>Code</th>
                <th>Title</th>
                <th>Units</th>
                <th>Department</th>
                <th>Grade Records</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($s = $subjects->fetch_assoc()): ?>
        <tr>
            <td class="td-number" style="font-weight:700"><?= htmlspecialchars($s['code']) ?></td>
            <td class="td-name"><?= htmlspecialchars($s['title']) ?></td>
            <td style="color: var(--primary); font-weight:700"><?= $s['units'] ?></td>
            <td class="td-muted"><?= htmlspecialchars($s['department']) ?></td>
            <td><?= $s['grade_records'] ?></td>
            <td>
                <div class="td-actions">
                    <a href="subjects.php?action=edit&id=<?= $s['id'] ?>" class="btn btn-sm btn-secondary">Edit</a>
                    <button onclick="confirmDelete('subjects.php?action=delete&id=<?= $s['id'] ?>', '<?= htmlspecialchars($s['code']) ?>')" class="btn btn-sm btn-danger">Delete</button>
                </div>
            </td>
        </tr>
        <?php endwhile; ?>
        <?php if ($count === 0): ?>
        <tr><td colspan="6">
            <div class="empty-state">
                <svg class="empty-icon" viewBox="0 0 24 24" fill="none"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" stroke="currentColor" stroke-width="2"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z" stroke="currentColor" stroke-width="2"/></svg>
                <h3>No subjects found</h3>
            </div>
        </td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once '../includes/footer.php'; ?>
