<?php
require_once '../includes/db.php';
$db = getDB();

// Top performing students (avg grade)
$top_students = $db->query("
    SELECT s.full_name, s.student_no, s.course,
           AVG(g.grade) as avg_grade,
           COUNT(g.id) as subject_count
    FROM students s
    JOIN grades g ON s.id = g.student_id
    GROUP BY s.id
    ORDER BY avg_grade DESC
    LIMIT 10
");

// Students by status
$total_students = $db->query("SELECT COUNT(*) as c FROM students")->fetch_assoc()['c'];
$status_data = $db->query("SELECT status, COUNT(*) as c FROM students GROUP BY status");

// Students by course
$course_data = $db->query("SELECT course, COUNT(*) as c FROM students GROUP BY course ORDER BY c DESC");

require_once '../includes/header.php';
?>

<div class="page-header">
    <div class="page-title">
        <h1>Reports</h1>
        <p>System-wide analytics and summaries</p>
    </div>
</div>

<div class="reports-grid">
    <!-- Top Performing Students -->
    <div class="card">
        <div class="card-body" style="padding-bottom:0">
            <div class="trophy-header">
                <span class="trophy-icon">🏆</span>
                <h2>Top Performing Students</h2>
            </div>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Student</th>
                    <th>Course</th>
                    <th>Avg Grade</th>
                    <th>Subjects</th>
                </tr>
            </thead>
            <tbody>
            <?php 
            $rank = 1;
            $rows = $top_students->fetch_all(MYSQLI_ASSOC);
            if (count($rows) === 0): ?>
            <tr><td colspan="5">
                <div class="empty-state" style="padding:40px 20px">
                    <p style="color:var(--text-light)">No grade data yet.</p>
                </div>
            </td></tr>
            <?php else: foreach ($rows as $r): 
                $medals = ['🥇','🥈','🥉'];
                $medal = $medals[$rank-1] ?? $rank;
                $avg = round($r['avg_grade'], 1);
                $color = $avg >= 90 ? 'var(--accent-green)' : ($avg >= 80 ? 'var(--accent-blue)' : 'var(--primary)');
            ?>
            <tr>
                <td style="font-size:18px;text-align:center"><?= $medal ?></td>
                <td>
                    <div style="font-weight:600"><?= htmlspecialchars($r['full_name']) ?></div>
                    <div style="font-size:12px;color:var(--text-light)"><?= htmlspecialchars($r['student_no']) ?></div>
                </td>
                <td class="td-muted" style="font-size:13px"><?= htmlspecialchars($r['course']) ?></td>
                <td style="font-weight:700;font-size:16px;color:<?= $color ?>"><?= $avg ?></td>
                <td style="text-align:center"><?= $r['subject_count'] ?></td>
            </tr>
            <?php $rank++; endforeach; endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Right column -->
    <div style="display:flex;flex-direction:column;gap:20px">

        <!-- Students by Status -->
        <div class="card">
            <div class="card-header"><h2>Students by Status</h2></div>
            <div class="card-body">
                <?php while ($s = $status_data->fetch_assoc()):
                    $pct = $total_students > 0 ? round(($s['c'] / $total_students) * 100) : 0;
                    $bar_class = strtolower($s['status']);
                ?>
                <div class="status-bar-item">
                    <div class="status-bar-header">
                        <span><?= htmlspecialchars($s['status']) ?></span>
                        <span><?= $s['c'] ?> (<?= $pct ?>%)</span>
                    </div>
                    <div class="status-bar-bg">
                        <div class="status-bar-fill <?= $bar_class ?>" data-width="<?= $pct ?>" style="width:0%"></div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>

        <!-- Students by Course -->
        <div class="card">
            <div class="card-header"><h2>Students by Course</h2></div>
            <div class="card-body" style="padding-top:8px;padding-bottom:8px">
                <?php while ($c = $course_data->fetch_assoc()): ?>
                <div class="course-list-item">
                    <span><?= htmlspecialchars($c['course']) ?></span>
                    <span class="course-list-count"><?= $c['c'] ?></span>
                </div>
                <?php endwhile; ?>
            </div>
        </div>

        <!-- Grade Stats -->
        <?php
        $grade_stats = $db->query("SELECT COUNT(*) as total, AVG(grade) as avg, MAX(grade) as max, MIN(grade) as min FROM grades")->fetch_assoc();
        ?>
        <div class="card">
            <div class="card-header"><h2>Grade Statistics</h2></div>
            <div class="card-body" style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                <div style="text-align:center;padding:12px;background:var(--bg-main);border-radius:10px">
                    <div style="font-size:28px;font-weight:700;color:var(--primary)"><?= $grade_stats['total'] ?? 0 ?></div>
                    <div style="font-size:12px;color:var(--text-light);font-weight:600;text-transform:uppercase;letter-spacing:0.5px">Total Records</div>
                </div>
                <div style="text-align:center;padding:12px;background:var(--bg-main);border-radius:10px">
                    <div style="font-size:28px;font-weight:700;color:var(--accent-blue)"><?= $grade_stats['avg'] ? round($grade_stats['avg'],1) : '—' ?></div>
                    <div style="font-size:12px;color:var(--text-light);font-weight:600;text-transform:uppercase;letter-spacing:0.5px">Average</div>
                </div>
                <div style="text-align:center;padding:12px;background:var(--bg-main);border-radius:10px">
                    <div style="font-size:28px;font-weight:700;color:var(--accent-green)"><?= $grade_stats['max'] ?? '—' ?></div>
                    <div style="font-size:12px;color:var(--text-light);font-weight:600;text-transform:uppercase;letter-spacing:0.5px">Highest</div>
                </div>
                <div style="text-align:center;padding:12px;background:var(--bg-main);border-radius:10px">
                    <div style="font-size:28px;font-weight:700;color:var(--accent-red)"><?= $grade_stats['min'] ?? '—' ?></div>
                    <div style="font-size:12px;color:var(--text-light);font-weight:600;text-transform:uppercase;letter-spacing:0.5px">Lowest</div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
