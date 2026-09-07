<?php
require_once __DIR__ . '/../includes/functions.php';
$pageTitle = 'Liên hệ / Hỗ trợ';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name === '') $errors[] = 'Vui lòng nhập họ tên.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email không hợp lệ.';
    if ($message === '') $errors[] = 'Vui lòng nhập nội dung.';

    if (!$errors) {
        submitContactMessage($name, $email, $subject, $message, $_SESSION['user_id'] ?? null);
        $success = true;
    }
}

include __DIR__ . '/includes/header.php';
?>

<section class="section" style="max-width:640px;">
  <div class="section-head"><h2>Liên hệ / Hỗ trợ khách hàng</h2></div>
  <p style="color:var(--muted); margin-top:-10px;">
    Cần hỗ trợ gấp? Bấm vào biểu tượng chat ở góc phải màn hình, hoặc gửi form dưới đây — đội ngũ SwiftMart sẽ phản hồi trong 24 giờ.
  </p>

  <?php if ($success): ?>
    <p style="color:var(--primary); font-weight:600; margin-top:20px;">
      Đã gửi yêu cầu thành công! Cảm ơn bạn đã liên hệ với SwiftMart.
    </p>
  <?php else: ?>
    <?php if ($errors): ?>
      <div style="color:var(--danger); margin-top:16px;">
        <?php foreach ($errors as $err) echo '<p style="margin:4px 0;">' . e($err) . '</p>'; ?>
      </div>
    <?php endif; ?>

    <form method="post" class="review-form" style="margin-top:20px; display:flex; flex-direction:column; gap:14px;">
      <div>
        <label style="font-size:14px; font-weight:600; display:block; margin-bottom:6px;">Họ tên</label>
        <input type="text" name="name" value="<?= e($_POST['name'] ?? '') ?>" style="width:100%; padding:10px; border:1px solid var(--border); border-radius:8px;">
      </div>
      <div>
        <label style="font-size:14px; font-weight:600; display:block; margin-bottom:6px;">Email</label>
        <input type="email" name="email" value="<?= e($_POST['email'] ?? '') ?>" style="width:100%; padding:10px; border:1px solid var(--border); border-radius:8px;">
      </div>
      <div>
        <label style="font-size:14px; font-weight:600; display:block; margin-bottom:6px;">Chủ đề</label>
        <input type="text" name="subject" value="<?= e($_POST['subject'] ?? '') ?>" placeholder="Ví dụ: Hỏi về đơn hàng #123" style="width:100%; padding:10px; border:1px solid var(--border); border-radius:8px;">
      </div>
      <div>
        <label style="font-size:14px; font-weight:600; display:block; margin-bottom:6px;">Nội dung</label>
        <textarea name="message" style="width:100%; min-height:120px; padding:10px; border:1px solid var(--border); border-radius:8px; font-family:inherit;"><?= e($_POST['message'] ?? '') ?></textarea>
      </div>
      <button type="submit" class="btn btn-primary" style="align-self:flex-start;">Gửi yêu cầu</button>
    </form>
  <?php endif; ?>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
