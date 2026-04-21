<?php
require_once 'includes/db.php';
$db = getDB();

// Stats
$total_students = $db->query("SELECT COUNT(*) as c FROM students")->fetch_assoc()['c'];
$active_students = $db->query("SELECT COUNT(*) as c FROM students WHERE status='Active'")->fetch_assoc()['c'];
$total_subjects = $db->query("SELECT COUNT(*) as c FROM subjects")->fetch_assoc()['c'];
$total_grades = $db->query("SELECT COUNT(*) as c FROM grades")->fetch_assoc()['c'];
$avg_grade_res = $db->query("SELECT AVG(grade) as avg FROM grades")->fetch_assoc();
$avg_grade = $avg_grade_res['avg'] ? round($avg_grade_res['avg'], 1) : 0;

// Recent students
$recent = $db->query("SELECT * FROM students ORDER BY created_at DESC LIMIT 5");

// Grade distribution
$grade_dist = [
    'A (90-100)' => 0, 'B (80-89)' => 0, 'C (70-79)' => 0, 'D (<70)' => 0
];
$gd = $db->query("SELECT grade FROM grades");
while ($r = $gd->fetch_assoc()) {
    $g = floatval($r['grade']);
    if ($g >= 90) $grade_dist['A (90-100)']++;
    elseif ($g >= 80) $grade_dist['B (80-89)']++;
    elseif ($g >= 70) $grade_dist['C (70-79)']++;
    else $grade_dist['D (<70)']++;
}

require_once 'includes/header.php';
?>

<div class="page-header">
    <div class="page-title">
        <h1>Dashboard</h1>
        <p>Overview of your student management system</p>
    </div>
    <a href="pages/students.php?action=add" class="btn btn-primary">
        <svg viewBox="0 0 24 24" fill="none"><line x1="12" y1="5" x2="12" y2="19" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/><line x1="5" y1="12" x2="19" y2="12" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/></svg>
        Add Student
    </a>
</div>

<!-- Stats -->
<div class="stats-grid">
    <div class="stat-card blue">
        <div class="stat-label">Total Students</div>
        <div class="stat-value"><?= $total_students ?></div>
        <div class="stat-sub"><?= $active_students ?> currently active</div>
    </div>
    <div class="stat-card blue">
        <div class="stat-label">Subjects Offered</div>
        <div class="stat-value" style="color: var(--accent-blue)"><?= $total_subjects ?></div>
        <div class="stat-sub">Across all departments</div>
    </div>
    <div class="stat-card green">
        <div class="stat-label">Grade Records</div>
        <div class="stat-value" style="color: var(--accent-green)"><?= $total_grades ?></div>
        <div class="stat-sub">Total enrolled grades</div>
    </div>
    <div class="stat-card blue">
        <div class="stat-label">Average Grade</div>
        <div class="stat-value"><?= $avg_grade ?: '—' ?></div>
        <div class="stat-sub">Across all subjects</div>
    </div>
</div>

<!-- Main grid -->
<div class="dashboard-grid">
    <!-- Recent Students -->
    <div class="card">
        <div class="card-header">
            <h2>Recent Students</h2>
            <a href="pages/students.php" class="btn btn-secondary btn-sm">View All</a>
        </div>
        <table id="recent-table">
            <thead>
                <tr>
                    <th>Student No.</th>
                    <th>Name</th>
                    <th>Course</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($s = $recent->fetch_assoc()): ?>
                <tr>
                    <td class="td-number"><?= htmlspecialchars($s['student_no']) ?></td>
                    <td class="td-name"><?= htmlspecialchars($s['full_name']) ?></td>
                    <td class="td-muted"><?= htmlspecialchars($s['course']) ?></td>
                    <td><span class="badge badge-<?= strtolower($s['status']) ?>"><?= strtoupper($s['status']) ?></span></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Grade Distribution -->
    <div class="card">
        <div class="card-header">
            <h2>Grade Distribution</h2>
            <a href="pages/grades.php" class="btn btn-secondary btn-sm">View Grades</a>
        </div>
        <div class="card-body">
            <?php foreach ($grade_dist as $label => $count): 
                $pct = $total_grades > 0 ? round(($count / $total_grades) * 100) : 0;
            ?>
            <div class="grade-dist-item">
                <div class="grade-dist-header">
                    <span><?= $label ?></span>
                    <span class="grade-dist-count"><?= $count ?> (<?= $pct ?>%)</span>
                </div>
                <div class="grade-bar-bg">
                    <div class="grade-bar-fill" data-width="<?= $pct ?>" style="width:0%"></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
