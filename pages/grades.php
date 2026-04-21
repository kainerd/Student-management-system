<?php
require_once '../includes/db.php';
$db = getDB();

$action = $_GET['action'] ?? '';
$edit_grade = null;

// DELETE
if ($action === 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $db->query("DELETE FROM grades WHERE id=$id");
    header("Location: grades.php?msg=deleted");
    exit;
}

// ADD / EDIT
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id'] ?? 0);
    $student_id = intval($_POST['student_id']);
    $subject_id = intval($_POST['subject_id']);
    $grade = floatval($_POST['grade']);
    $semester = $db->real_escape_string($_POST['semester']);
    $school_year = $db->real_escape_string(trim($_POST['school_year']));
    
    // Auto remarks
    if ($grade >= 90) $remarks = 'Excellent';
    elseif ($grade >= 80) $remarks = 'Very Good';
    elseif ($grade >= 70) $remarks = 'Good';
    else $remarks = 'Failed';

    if ($id > 0) {
        $db->query("UPDATE grades SET student_id=$student_id, subject_id=$subject_id, grade=$grade, semester='$semester', school_year='$school_year', remarks='$remarks' WHERE id=$id");
        header("Location: grades.php?msg=updated");
    } else {
        $db->query("INSERT INTO grades (student_id, subject_id, grade, semester, school_year, remarks) VALUES ($student_id, $subject_id, $grade, '$semester', '$school_year', '$remarks')");
        header("Location: grades.php?msg=added");
    }
    exit;
}

if ($action === 'edit' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $edit_grade = $db->query("SELECT * FROM grades WHERE id=$id")->fetch_assoc();
}

// Filters
$filter_year = intval($_GET['year'] ?? 0);
$filter_sem = $db->real_escape_string($_GET['semester'] ?? '');

$where = [];
if ($filter_year) $where[] = "g.school_year LIKE '%$filter_year%'";
if ($filter_sem) $where[] = "g.semester='$filter_sem'";

// Also filter by student if coming from student grades button
$filter_student = intval($_GET['student_id'] ?? 0);
if ($filter_student) $where[] = "g.student_id=$filter_student";

$where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$grades = $db->query("SELECT g.*, s.full_name, s.student_no, sub.code as subject_code, sub.title as subject_title FROM grades g JOIN students s ON g.student_id=s.id JOIN subjects sub ON g.subject_id=sub.id $where_sql ORDER BY g.created_at DESC");
$count = $db->query("SELECT COUNT(*) as c FROM grades g $where_sql")->fetch_assoc()['c'];

// For dropdowns in modal
$all_students = $db->query("SELECT id, student_no, full_name FROM students ORDER BY full_name");
$all_subjects = $db->query("SELECT id, code, title FROM subjects ORDER BY code");

$msgs = ['added' => 'Grade added!', 'updated' => 'Grade updated!', 'deleted' => 'Grade deleted.'];
$message = isset($_GET['msg']) ? ($msgs[$_GET['msg']] ?? '') : '';

require_once '../includes/header.php';
$open_modal = ($action === 'add' || $action === 'edit');

// Build student/subject options
$student_opts = '';
while ($r = $all_students->fetch_assoc()) {
    $sel = ($edit_grade && $edit_grade['student_id'] == $r['id']) ? 'selected' : '';
    $student_opts .= "<option value='{$r['id']}' $sel>{$r['student_no']} - {$r['full_name']}</option>";
}
$subject_opts = '';
while ($r = $all_subjects->fetch_assoc()) {
    $sel = ($edit_grade && $edit_grade['subject_id'] == $r['id']) ? 'selected' : '';
    $subject_opts .= "<option value='{$r['id']}' $sel>{$r['code']} - {$r['title']}</option>";
}
$semesters = ['1st Semester', '2nd Semester', 'Summer'];
$sem_opts = '';
foreach ($semesters as $sem) {
    $sel = ($edit_grade && $edit_grade['semester'] === $sem) ? 'selected' : '';
    $sem_opts .= "<option value='$sem' $sel>$sem</option>";
}
?>

<?php if ($message): ?>
<script>document.addEventListener('DOMContentLoaded', () => showToast("<?= $message ?>", "<?= strpos($message,'delet') !== false ? 'error' : 'success' ?>"));</script>
<?php endif; ?>

<?php if ($open_modal): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const formHtml = `
        <form method="POST" action="grades.php">
            <input type="hidden" name="id" value="<?= $edit_grade['id'] ?? 0 ?>">
            <div class="form-group">
                <label class="form-label">Student</label>
                <select class="form-control" name="student_id" required>
                    <option value="">-- Select Student --</option>
                    <?= $student_opts ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Subject</label>
                <select class="form-control" name="subject_id" required>
                    <option value="">-- Select Subject --</option>
                    <?= $subject_opts ?>
                </select>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Grade (0-100)</label>
                    <input class="form-control" type="number" name="grade" min="0" max="100" step="0.01" required value="<?= $edit_grade['grade'] ?? '' ?>" placeholder="88.5">
                </div>
                <div class="form-group">
                    <label class="form-label">School Year</label>
                    <input class="form-control" name="school_year" required value="<?= htmlspecialchars($edit_grade['school_year'] ?? '2024-2025') ?>" placeholder="2024-2025">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Semester</label>
                <select class="form-control" name="semester" required>
                    <?= $sem_opts ?>
                </select>
            </div>
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn btn-primary"><?= $edit_grade ? 'Update Grade' : 'Add Grade' ?></button>
            </div>
        </form>`;
    openModal('<?= $edit_grade ? 'Edit Grade' : 'Add Grade Record' ?>', formHtml);
});
</script>
<?php endif; ?>

<div class="page-header">
    <div class="page-title">
        <h1>Grades</h1>
        <p><?= $count ?> grade record(s)</p>
    </div>
    <a href="grades.php?action=add" class="btn btn-primary">
        <svg viewBox="0 0 24 24" fill="none"><line x1="12" y1="5" x2="12" y2="19" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/><line x1="5" y1="12" x2="19" y2="12" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/></svg>
        Add Grade
    </a>
</div>

<form method="GET" action="grades.php" style="margin-bottom:20px">
    <div class="filter-bar">
        <select name="year" class="select-filter">
            <option value="">All Years</option>
            <?php for ($y = date('Y'); $y >= 2020; $y--): ?>
            <option value="<?= $y ?>" <?= $filter_year == $y ? 'selected' : '' ?>><?= $y ?></option>
            <?php endfor; ?>
        </select>
        <select name="semester" class="select-filter">
            <option value="">All Semesters</option>
            <?php foreach ($semesters as $sem): ?>
            <option value="<?= $sem ?>" <?= $filter_sem === $sem ? 'selected' : '' ?>><?= $sem ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-secondary">Filter</button>
        <a href="grades.php" class="btn btn-secondary">Clear</a>
    </div>
</form>

<div class="table-wrapper">
    <table>
        <thead>
            <tr>
                <th>Student No.</th>
                <th>Student Name</th>
                <th>Subject</th>
                <th>Grade</th>
                <th>Semester</th>
                <th>School Year</th>
                <th>Remarks</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($g = $grades->fetch_assoc()): 
            $grade_val = floatval($g['grade']);
            $grade_class = $grade_val >= 90 ? 'style="color:var(--accent-green);font-weight:700"' : 
                          ($grade_val >= 80 ? 'style="color:var(--accent-blue);font-weight:700"' : 
                          ($grade_val >= 70 ? 'style="color:var(--primary);font-weight:700"' : 'style="color:var(--accent-red);font-weight:700"'));
        ?>
        <tr>
            <td class="td-number"><?= htmlspecialchars($g['student_no']) ?></td>
            <td class="td-name"><?= htmlspecialchars($g['full_name']) ?></td>
            <td><span style="font-weight:600"><?= htmlspecialchars($g['subject_code']) ?></span> <span class="td-muted">— <?= htmlspecialchars($g['subject_title']) ?></span></td>
            <td <?= $grade_class ?>><?= number_format($grade_val, 1) ?></td>
            <td class="td-muted"><?= htmlspecialchars($g['semester']) ?></td>
            <td class="td-muted"><?= htmlspecialchars($g['school_year']) ?></td>
            <td><?= htmlspecialchars($g['remarks'] ?? '') ?></td>
            <td>
                <div class="td-actions">
                    <a href="grades.php?action=edit&id=<?= $g['id'] ?>" class="btn btn-sm btn-secondary">Edit</a>
                    <button onclick="confirmDelete('grades.php?action=delete&id=<?= $g['id'] ?>', 'this grade record')" class="btn btn-sm btn-danger">Delete</button>
                </div>
            </td>
        </tr>
        <?php endwhile; ?>
        <?php if ($count === 0): ?>
        <tr><td colspan="8">
            <div class="empty-state">
                <svg class="empty-icon" viewBox="0 0 24 24" fill="none"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12" stroke="currentColor" stroke-width="2"/></svg>
                <h3>No grade records found</h3>
                <a href="grades.php?action=add">Add the first grade record</a>
            </div>
        </td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once '../includes/footer.php'; ?>
