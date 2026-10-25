<?php
session_start();
require __DIR__ . '/config.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function e($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
function redirect(string $url = 'index.php'): void {
    header('Location: ' . $url);
    exit;
}

$errors = [];
$editStudent = null;
$flash = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);

// Handle create, update and delete actions.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $token)) {
        http_response_code(403);
        exit('Invalid request token. Refresh the page and try again.');
    }

    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if ($id) {
            $stmt = $pdo->prepare('DELETE FROM students WHERE id = ?');
            $stmt->execute([$id]);
            $_SESSION['flash'] = $stmt->rowCount() ? 'Student record deleted.' : 'Student record was not found.';
        }
        redirect();
    }

    if ($action === 'save') {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: null;
        $name = trim($_POST['name'] ?? '');
        $rollNumber = trim($_POST['roll_number'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $course = trim($_POST['course'] ?? '');
        $semester = filter_input(INPUT_POST, 'semester', FILTER_VALIDATE_INT);
        $phone = trim($_POST['phone'] ?? '');

        if ($name === '' || mb_strlen($name) > 100) $errors[] = 'Name is required and must be under 100 characters.';
        if ($rollNumber === '' || mb_strlen($rollNumber) > 30) $errors[] = 'Roll number is required and must be under 30 characters.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 150) $errors[] = 'Enter a valid email address.';
        if ($course === '' || mb_strlen($course) > 100) $errors[] = 'Course is required and must be under 100 characters.';
        if (!$semester || $semester < 1 || $semester > 12) $errors[] = 'Semester must be between 1 and 12.';
        if ($phone !== '' && !preg_match('/^[0-9+()\- ]{7,20}$/', $phone)) $errors[] = 'Enter a valid phone number (7–20 characters).';

        if (!$errors) {
            try {
                if ($id) {
                    $stmt = $pdo->prepare('UPDATE students SET name=?, roll_number=?, email=?, course=?, semester=?, phone=? WHERE id=?');
                    $stmt->execute([$name, $rollNumber, $email, $course, $semester, $phone ?: null, $id]);
                    $_SESSION['flash'] = 'Student details updated successfully.';
                } else {
                    $stmt = $pdo->prepare('INSERT INTO students (name, roll_number, email, course, semester, phone) VALUES (?, ?, ?, ?, ?, ?)');
                    $stmt->execute([$name, $rollNumber, $email, $course, $semester, $phone ?: null]);
                    $_SESSION['flash'] = 'Student added successfully.';
                }
                redirect();
            } catch (PDOException $ex) {
                if ($ex->getCode() === '23000') {
                    $errors[] = 'That roll number or email is already registered.';
                } else {
                    $errors[] = 'Could not save the record. Check the database setup and try again.';
                }
            }
        }
        $editStudent = ['id'=>$id, 'name'=>$name, 'roll_number'=>$rollNumber, 'email'=>$email, 'course'=>$course, 'semester'=>$semester, 'phone'=>$phone];
    }
}

// Load a record into the form for editing.
if (isset($_GET['edit']) && !$editStudent) {
    $editId = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT);
    if ($editId) {
        $stmt = $pdo->prepare('SELECT * FROM students WHERE id = ?');
        $stmt->execute([$editId]);
        $editStudent = $stmt->fetch() ?: null;
    }
}

$search = trim($_GET['q'] ?? '');
if ($search !== '') {
    $like = '%' . $search . '%';
    $stmt = $pdo->prepare('SELECT * FROM students WHERE name LIKE ? OR roll_number LIKE ? OR course LIKE ? OR email LIKE ? ORDER BY created_at DESC');
    $stmt->execute([$like, $like, $like, $like]);
    $students = $stmt->fetchAll();
} else {
    $students = $pdo->query('SELECT * FROM students ORDER BY created_at DESC')->fetchAll();
}
$totalStudents = (int)$pdo->query('SELECT COUNT(*) FROM students')->fetchColumn();
$totalCourses = (int)$pdo->query('SELECT COUNT(DISTINCT course) FROM students')->fetchColumn();
$semesterStmt = $pdo->query('SELECT COUNT(DISTINCT semester) FROM students');
$totalSemesters = (int)$semesterStmt->fetchColumn();

$form = $editStudent ?? ['id'=>null, 'name'=>'', 'roll_number'=>'', 'email'=>'', 'course'=>'', 'semester'=>'', 'phone'=>''];
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>StudentHub | Student Management System</title>
  <link rel="stylesheet" href="assets/style.css">
  <script src="assets/app.js" defer></script>
</head>
<body>
<div class="app-shell">
  <aside class="sidebar">
    <a class="brand" href="index.php"><span class="brand-mark">S</span><span>Student<span class="brand-light">Hub</span><small>MANAGEMENT PORTAL</small></span></a>
    <p class="nav-label">WORKSPACE</p>
    <a class="nav-link active" href="index.php"><span>▦</span> Dashboard</a>
    <a class="nav-link" href="#student-form"><span>＋</span> Add student</a>
    <div class="sidebar-note"><div class="note-icon">✦</div><strong>Stay organised</strong><p>Keep student records clear, current and easy to find.</p></div>
    <div class="sidebar-footer"><span class="status-dot"></span> Local development mode</div>
  </aside>

  <main class="main-content">
    <header class="topbar"><div><p class="eyebrow">ACADEMIC ADMINISTRATION</p><h1>Student dashboard</h1></div><div class="admin-chip"><span class="avatar">A</span><span><strong>Administrator</strong><small>Project demo</small></span></div></header>

    <?php if ($flash): ?><div class="alert success" role="status"><?= e($flash) ?></div><?php endif; ?>
    <?php if ($errors): ?><div class="alert error" role="alert"><strong>Please fix the following:</strong><ul><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>

    <section class="stats-grid" aria-label="Student statistics">
      <article class="stat-card"><div class="stat-top"><span>Total students</span><span class="stat-icon purple">♙</span></div><div class="stat-number"><?= $totalStudents ?></div><p>All registered records</p></article>
      <article class="stat-card"><div class="stat-top"><span>Courses represented</span><span class="stat-icon blue">▤</span></div><div class="stat-number"><?= $totalCourses ?></div><p>Unique course names</p></article>
      <article class="stat-card"><div class="stat-top"><span>Semesters represented</span><span class="stat-icon green">◷</span></div><div class="stat-number"><?= $totalSemesters ?></div><p>Across all student records</p></article>
    </section>

    <section class="content-grid">
      <section class="panel form-panel" id="student-form">
        <div class="panel-heading"><div><p class="eyebrow">STUDENT DIRECTORY</p><h2><?= $form['id'] ? 'Edit student' : 'Add a student' ?></h2></div><span class="heading-symbol">＋</span></div>
        <p class="panel-description">Enter the student's details below.</p>
        <form method="post" action="index.php" id="studentForm" novalidate>
          <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
          <input type="hidden" name="action" value="save">
          <input type="hidden" name="id" value="<?= e($form['id']) ?>">
          <div class="field"><label for="name">Full name <span>*</span></label><input id="name" name="name" type="text" maxlength="100" required placeholder="e.g. Aarya Patel" value="<?= e($form['name']) ?>"></div>
          <div class="field-row"><div class="field"><label for="roll_number">Roll number <span>*</span></label><input id="roll_number" name="roll_number" maxlength="30" required placeholder="e.g. CSE023" value="<?= e($form['roll_number']) ?>"></div><div class="field"><label for="semester">Semester <span>*</span></label><select id="semester" name="semester" required><option value="">Select</option><?php for ($s=1; $s<=12; $s++): ?><option value="<?= $s ?>" <?= (string)$form['semester']===(string)$s?'selected':'' ?>>Semester <?= $s ?></option><?php endfor; ?></select></div></div>
          <div class="field"><label for="email">Email address <span>*</span></label><input id="email" name="email" type="email" maxlength="150" required placeholder="student@example.com" value="<?= e($form['email']) ?>"></div>
          <div class="field"><label for="course">Course / department <span>*</span></label><input id="course" name="course" maxlength="100" required placeholder="e.g. B.Tech CSE" value="<?= e($form['course']) ?>"></div>
          <div class="field"><label for="phone">Phone number <small>(optional)</small></label><input id="phone" name="phone" type="tel" maxlength="20" placeholder="e.g. 9876543210" value="<?= e($form['phone']) ?>"></div>
          <div class="form-actions"><button class="btn btn-primary" type="submit"><?= $form['id'] ? 'Save changes' : 'Add student' ?> <span>→</span></button><?php if ($form['id']): ?><a class="btn btn-quiet" href="index.php">Cancel</a><?php else: ?><button class="btn btn-quiet" type="reset">Clear</button><?php endif; ?></div>
        </form>
      </section>

      <section class="panel directory-panel">
        <div class="panel-heading directory-heading"><div><p class="eyebrow">RECORDS</p><h2>Student directory</h2></div><span class="record-count"><?= count($students) ?> shown</span></div>
        <form class="search-form" method="get" action="index.php"><label class="search-box"><span>⌕</span><input type="search" name="q" value="<?= e($search) ?>" placeholder="Search name, roll no., course…" aria-label="Search students"></label><button class="btn btn-secondary" type="submit">Search</button><?php if ($search !== ''): ?><a class="clear-search" href="index.php">Clear</a><?php endif; ?></form>
        <div class="table-wrap"><table><thead><tr><th>STUDENT</th><th>ROLL NO.</th><th>COURSE</th><th>SEMESTER</th><th>ACTIONS</th></tr></thead><tbody>
        <?php if (!$students): ?><tr><td colspan="5" class="empty-state"><span>⌕</span><strong>No students found</strong><small><?= $search !== '' ? 'Try a different search term.' : 'Add your first student using the form.' ?></small></td></tr><?php endif; ?>
        <?php foreach ($students as $student): ?><tr><td><div class="student-cell"><span class="student-avatar"><?= e(mb_strtoupper(mb_substr($student['name'],0,1))) ?></span><span><strong><?= e($student['name']) ?></strong><small><?= e($student['email']) ?></small></span></div></td><td><span class="roll-pill"><?= e($student['roll_number']) ?></span></td><td><?= e($student['course']) ?></td><td><span class="semester-pill">Sem <?= e($student['semester']) ?></span></td><td><div class="actions"><a class="action-link edit" href="index.php?edit=<?= (int)$student['id'] ?>#student-form" aria-label="Edit <?= e($student['name']) ?>">Edit</a><form method="post" action="index.php" class="delete-form" data-student-name="<?= e($student['name']) ?>"><input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$student['id'] ?>"><button class="action-link delete" type="submit">Delete</button></form></div></td></tr><?php endforeach; ?>
        </tbody></table></div>
        <div class="table-footer"><span>Showing <?= count($students) ?> of <?= $totalStudents ?> total students</span><span class="live-label"><i></i> Database connected</span></div>
      </section>
    </section>
    <footer class="page-footer">StudentHub <span>·</span> Student Management System <span>·</span> Academic project demo</footer>
  </main>
</div>
</body>
</html>
