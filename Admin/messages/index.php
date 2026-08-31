<?php
/**
 * ICK News - Contact messages inbox + reader/reply.
 * Lists messages from the public contact page. Open one to read it fully and
 * reply: the reply is emailed to the sender FROM the "reply-from" address set
 * in Settings (settings.contact_reply_email), and saved on the message.
 */
require_once __DIR__ . '/../config/auth.php';
require_can('publish');

$pageTitle  = 'Messages';
$activeMenu = 'messages';
$basePath   = '../';

$replyError = null;
$replyOk    = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';
    $id     = (int) ($_POST['id'] ?? 0);

    if ($id > 0 && $action === 'reply') {
        // Load the message + the reply-from address.
        $msg = db()->prepare('SELECT * FROM contact_messages WHERE id=? LIMIT 1');
        $msg->execute([$id]);
        $msg = $msg->fetch();
        $replyBody = trim($_POST['reply_body'] ?? '');
        $replyFrom = trim(setting('contact_reply_email') ?: setting('contact_email'));

        if (!$msg) {
            set_flash('danger', 'Message not found.');
            redirect('messages/index.php');
        } elseif ($replyBody === '') {
            $replyError = 'Please type a reply.';
        } elseif (!$replyFrom || !filter_var($replyFrom, FILTER_VALIDATE_EMAIL)) {
            $replyError = 'Set a valid "Reply-from email" in Settings before replying.';
        } else {
            // Compose + send. mail() may not deliver on local XAMPP, but we ALWAYS
            // record the reply so it is never lost and works once mail is configured.
            $to      = $msg['email'];
            $subject = 'RE: ' . ($msg['subject'] ?: ('Your message to ' . APP_NAME));
            $headers = 'From: ' . APP_NAME . ' <' . $replyFrom . ">\r\n"
                     . 'Reply-To: ' . $replyFrom . "\r\n"
                     . 'Content-Type: text/plain; charset=UTF-8';
            $sent = @mail($to, $subject, $replyBody, $headers);

            db()->prepare('UPDATE contact_messages SET reply_body=?, replied_at=NOW(), is_read=1 WHERE id=?')
                ->execute([$replyBody, $id]);

            set_flash($sent ? 'success' : 'warning',
                $sent
                    ? 'Reply sent to ' . $to . '.'
                    : 'Reply saved. (Email could not be sent from this server — it will send once mail/SMTP is configured.)');
            redirect('messages/index.php?id=' . $id);
        }
    } elseif ($id > 0 && $action === 'read') {
        db()->prepare('UPDATE contact_messages SET is_read=1 WHERE id=?')->execute([$id]);
        redirect('messages/index.php');
    } elseif ($id > 0 && $action === 'unread') {
        db()->prepare('UPDATE contact_messages SET is_read=0 WHERE id=?')->execute([$id]);
        redirect('messages/index.php');
    } elseif ($id > 0 && $action === 'delete') {
        db()->prepare('DELETE FROM contact_messages WHERE id=?')->execute([$id]);
        set_flash('success', 'Message deleted.');
        redirect('messages/index.php');
    }
}

// ---- Single-message view (?id=) ----
$viewId = (int) ($_GET['id'] ?? 0);
$view   = null;
if ($viewId > 0) {
    $st = db()->prepare('SELECT * FROM contact_messages WHERE id=? LIMIT 1');
    $st->execute([$viewId]);
    $view = $st->fetch();
    if ($view && !$view['is_read']) {
        db()->prepare('UPDATE contact_messages SET is_read=1 WHERE id=?')->execute([$viewId]);
        $view['is_read'] = 1;
    }
}

$messages = db()->query('SELECT * FROM contact_messages ORDER BY is_read ASC, created_at DESC')->fetchAll();
$unread   = (int) db()->query('SELECT COUNT(*) FROM contact_messages WHERE is_read=0')->fetchColumn();
$replyFromSet = trim(setting('contact_reply_email') ?: '');

require_once __DIR__ . '/../header.php';
?>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            <div class="row mb-3 align-items-center">
                <div class="col">
                    <h4 class="mb-0">Contact Messages</h4>
                    <p class="text-muted mb-0"><?= $unread ?> unread.
                        <?php if (!$replyFromSet): ?>
                            <span class="text-danger">Set a “Reply-from email” in <a href="settings/index">Settings</a> to reply.</span>
                        <?php else: ?>
                            Replies are sent from <b><?= e($replyFromSet) ?></b>.
                        <?php endif; ?>
                    </p>
                </div>
                <?php if ($view): ?>
                <div class="col-auto"><a href="messages/index" class="btn btn-light"><i class="ri-arrow-left-line align-bottom"></i> Back to inbox</a></div>
                <?php endif; ?>
            </div>

            <?php if ($view): /* ===== SINGLE MESSAGE + REPLY ===== */ ?>
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between flex-wrap mb-2">
                            <div>
                                <h5 class="mb-1"><?= e($view['subject'] ?: '(no subject)') ?></h5>
                                <div>From <strong><?= e($view['name']) ?></strong> &lt;<a href="mailto:<?= e($view['email']) ?>"><?= e($view['email']) ?></a>&gt;</div>
                            </div>
                            <small class="text-muted"><?= e(date('M j, Y H:i', strtotime($view['created_at']))) ?></small>
                        </div>
                        <hr>
                        <p style="white-space:pre-wrap;line-height:1.7;"><?= e($view['body']) ?></p>
                    </div>
                </div>

                <?php if (!empty($view['reply_body'])): ?>
                <div class="card border-success">
                    <div class="card-header bg-success-subtle text-success">
                        <i class="ri-reply-line"></i> Replied <?= e(date('M j, Y H:i', strtotime($view['replied_at']))) ?>
                    </div>
                    <div class="card-body"><p style="white-space:pre-wrap;line-height:1.7;margin:0;"><?= e($view['reply_body']) ?></p></div>
                </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0"><?= empty($view['reply_body']) ? 'Reply' : 'Reply again' ?></h5></div>
                    <div class="card-body">
                        <?php if ($replyError): ?><div class="alert alert-danger"><?= e($replyError) ?></div><?php endif; ?>
                        <form method="post">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="reply">
                            <input type="hidden" name="id" value="<?= (int) $view['id'] ?>">
                            <div class="mb-2">
                                <label class="form-label">To</label>
                                <input type="text" class="form-control" value="<?= e($view['name']) ?> &lt;<?= e($view['email']) ?>&gt;" disabled>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Your reply</label>
                                <textarea name="reply_body" class="form-control" rows="6" placeholder="Type your reply…" required></textarea>
                                <small class="text-muted">Sent from <b><?= e($replyFromSet ?: '(not set)') ?></b> to the sender's email.</small>
                            </div>
                            <button class="btn btn-primary" <?= $replyFromSet ? '' : 'disabled' ?>>
                                <i class="ri-send-plane-line align-bottom me-1"></i> Send reply
                            </button>
                        </form>
                    </div>
                </div>

            <?php else: /* ===== INBOX LIST ===== */ ?>
                <div class="card">
                    <div class="card-body">
                        <?php if (!$messages): ?>
                            <p class="text-center text-muted py-4 mb-0">No messages yet.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-centered align-middle mb-0">
                                    <thead class="table-light text-muted">
                                        <tr>
                                            <th>From</th><th>Subject</th><th>Preview</th><th>Received</th><th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($messages as $m): ?>
                                        <tr<?= $m['is_read'] ? '' : ' class="fw-semibold"' ?>>
                                            <td>
                                                <?php if (!$m['is_read']): ?><span class="badge bg-danger-subtle text-danger me-1">NEW</span><?php endif; ?>
                                                <?php if (!empty($m['reply_body'])): ?><span class="badge bg-success-subtle text-success me-1">REPLIED</span><?php endif; ?>
                                                <div><?= e($m['name']) ?></div>
                                                <small class="text-muted"><?= e($m['email']) ?></small>
                                            </td>
                                            <td><?= e($m['subject'] ?: '—') ?></td>
                                            <td class="text-muted"><?= e(mb_strimwidth((string) $m['body'], 0, 60, '…')) ?></td>
                                            <td><small class="text-muted"><?= e(date('M j, Y H:i', strtotime($m['created_at']))) ?></small></td>
                                            <td class="text-end text-nowrap">
                                                <a href="messages/index?id=<?= (int) $m['id'] ?>" class="btn btn-sm btn-soft-primary" title="Open / Reply"><i class="ri-eye-line"></i></a>
                                                <form method="post" class="d-inline" onsubmit="return confirm('Delete this message?');">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?= (int) $m['id'] ?>">
                                                    <button class="btn btn-sm btn-soft-danger" title="Delete"><i class="ri-delete-bin-line"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>

    <footer class="footer">
        <div class="container-fluid">
            <div class="row"><div class="col-sm-6"><script>document.write(new Date().getFullYear())</script> © <?= e(APP_NAME) ?>.</div></div>
        </div>
    </footer>
</div>

<?php require_once __DIR__ . '/../footer.php'; ?>
