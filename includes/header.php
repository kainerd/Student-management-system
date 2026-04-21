<?php
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduTrack - <?= ucfirst($current_page) ?></title>
    <link rel="stylesheet" href="<?= (strpos($_SERVER['PHP_SELF'], '/pages/') !== false) ? '../' : '' ?>assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=DM+Serif+Display&display=swap" rel="stylesheet">
</head>
<body>
<nav class="navbar">
    <div class="nav-brand">
        <div class="nav-logo">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <span class="brand-text">Edu<strong>Track</strong></span>
    </div>
    <ul class="nav-links">
        <li><a href="<?= (strpos($_SERVER['PHP_SELF'], '/pages/') !== false) ? '../' : '' ?>index.php" class="<?= $current_page === 'index' ? 'active' : '' ?>">
            <svg viewBox="0 0 24 24" fill="none"><rect x="3" y="3" width="7" height="7" rx="1" stroke="currentColor" stroke-width="2"/><rect x="14" y="3" width="7" height="7" rx="1" stroke="currentColor" stroke-width="2"/><rect x="3" y="14" width="7" height="7" rx="1" stroke="currentColor" stroke-width="2"/><rect x="14" y="14" width="7" height="7" rx="1" stroke="currentColor" stroke-width="2"/></svg>
            Dashboard
        </a></li>
        <li><a href="<?= (strpos($_SERVER['PHP_SELF'], '/pages/') !== false) ? '' : 'pages/' ?>students.php" class="<?= $current_page === 'students' ? 'active' : '' ?>">
            <svg viewBox="0 0 24 24" fill="none"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="2"/><path d="M23 21v-2a4 4 0 0 0-3-3.87" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M16 3.13a4 4 0 0 1 0 7.75" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            Students
        </a></li>
        <li><a href="<?= (strpos($_SERVER['PHP_SELF'], '/pages/') !== false) ? '' : 'pages/' ?>subjects.php" class="<?= $current_page === 'subjects' ? 'active' : '' ?>">
            <svg viewBox="0 0 24 24" fill="none"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z" stroke="currentColor" stroke-width="2"/></svg>
            Subjects
        </a></li>
        <li><a href="<?= (strpos($_SERVER['PHP_SELF'], '/pages/') !== false) ? '' : 'pages/' ?>grades.php" class="<?= $current_page === 'grades' ? 'active' : '' ?>">
            <svg viewBox="0 0 24 24" fill="none"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Grades
        </a></li>
        <li><a href="<?= (strpos($_SERVER['PHP_SELF'], '/pages/') !== false) ? '' : 'pages/' ?>reports.php" class="<?= $current_page === 'reports' ? 'active' : '' ?>">
            <svg viewBox="0 0 24 24" fill="none"><line x1="18" y1="20" x2="18" y2="10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><line x1="12" y1="20" x2="12" y2="4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><line x1="6" y1="20" x2="6" y2="14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            Reports
        </a></li>
    </ul>
    <div class="nav-badge">XAMPP / MySQL</div>
</nav>
<main class="main-content">
