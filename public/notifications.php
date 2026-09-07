<?php
require_once __DIR__ . '/../includes/functions.php';
$pageTitle = 'Thông báo';

$loggedIn = !empty($_SESSION['user_id']);
$notifications = $loggedIn ? getUserNotifications($_SESSION['user_id']) : [];

include __DIR__ . '/includes/header.php';
?>

<section class="section">
  <div class="section-head">
    <h2>Thông báo</h2>
    <?php if ($loggedIn && $notifications): ?>
      <a href="#" onclick="markAllRead(event)">Đánh dấu tất cả đã đọc</a>
    <?php endif; ?>
  </div>

  <?php if (!$loggedIn): ?>
    <div class="empty-state">
      Vui lòng đăng nhập để xem thông báo.<br><br>
      <a href="<?= BASE_URL ?>login.php" class="btn btn-primary">Đăng nhập</a>
    </div>
  <?php elseif ($notifications): ?>
    <div id="notification-list">
      <?php foreach ($notifications as $n): ?>
        <div class="review-item" id="noti-<?= $n['id'] ?>" style="<?= $n['is_read'] ? 'opacity:.6;' : 'background:#F0FDF4;' ?>">
          <div class="review-head">
            <span><?= e($n['title']) ?></span>
            <span style="color:var(--muted); font-weight:400; font-size:12px;"><?= date('d/m/Y H:i', strtotime($n['created_at'])) ?></span>
          </div>
          <?php if ($n['message']): ?>
            <p style="margin:8px 0 0; color:var(--muted);"><?= e($n['message']) ?></p>
          <?php endif; ?>
          <?php if (!$n['is_read']): ?>
            <button class="btn btn-outline" style="margin-top:8px; padding:6px 14px; font-size:13px;" onclick="markRead(<?= $n['id'] ?>, this)">Đánh dấu đã đọc</button>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <div class="empty-state">Bạn chưa có thông báo nào.</div>
  <?php endif; ?>
</section>

<script>
function markRead(id, btn) {
  fetch('<?= BASE_URL ?>ajax/notification-read.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({ id: id })
  }).then(res => res.json()).then(data => {
    if (data.success) {
      document.getElementById('noti-' + id).style.opacity = '.6';
      document.getElementById('noti-' + id).style.background = 'transparent';
      btn.remove();
    }
  });
}

function markAllRead(e) {
  e.preventDefault();
  fetch('<?= BASE_URL ?>ajax/notification-read.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({ all: true })
  }).then(res => res.json()).then(data => {
    if (data.success) location.reload();
  });
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
