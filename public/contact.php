<?php
/**
 * UR KAMINUZA - Contact page.
 * Uses the theme's contact.html template (About + Contact Info + Map + form),
 * but all content is dynamic: contact details + Google Map come from Admin
 * Settings, and the form stores messages in contact_messages for staff.
 */
require_once __DIR__ . '/include/content.php';

$notice = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['do'] ?? '') === 'contact') {
    $name    = trim(($_POST['name'] ?? '') . ' ' . ($_POST['surname'] ?? ''));
    $email   = trim($_POST['email'] ?? '');
    $phone   = trim($_POST['phone'] ?? '');
    $body    = trim($_POST['message'] ?? '');
    $subject = $phone !== '' ? ('Phone: ' . $phone) : null;
    $isBot   = trim($_POST['website'] ?? '') !== '';   // honeypot

    if ($name === '' || $email === '' || $body === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $isBot) {
        $notice = ['type' => 'err', 'msg' => t('msg_error')];
    } else {
        $stmt = db()->prepare(
            'INSERT INTO contact_messages (name, email, subject, body, ip) VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            mb_substr($name, 0, 120),
            mb_substr($email, 0, 150),
            $subject ? mb_substr($subject, 0, 200) : null,
            mb_substr($body, 0, 5000),
            @inet_pton($_SERVER['REMOTE_ADDR'] ?? '') ?: null,
        ]);
        $notice = ['type' => 'ok', 'msg' => t('msg_sent')];
    }
}

$cEmail   = setting('contact_email');
$cPhone   = setting('contact_phone');
$cAddress = setting('address');
$cWebsite = setting('contact_website');
$mapEmbed = map_embed_src(setting('contact_map_embed'));

require __DIR__ . '/header.php';
?>

<!--========== BEGIN #MAIN-SECTION ==========-->
<div id="main-section">
  <!--========== BEGIN .MODULE ==========-->
  <section class="module-top">
    <div class="container">

      <?php if ($notice): ?>
        <div class="kz-note kz-note--<?= $notice['type'] === 'ok' ? 'ok' : 'err' ?>"><?= e($notice['msg']) ?></div>
      <?php endif; ?>

      <div class="row no-gutter">
        <!-- Begin .contact-us -->
        <div class="contact-us">
          <div class="col-xs-12 col-sm-5 col-md-5">
            <div class="form-group">
              <div class="title-left title-style04 underline04">
                <h3><?= e(t("about_us")) ?></h3>
              </div>
              <p><?= e(setting('about') ?: (site_name() . ' — ' . (site_tagline() ?: ''))) ?></p>
              <div class="title-left title-style04 underline04">
                <h3><?= e(t("contact_us")) ?></h3>
              </div>
              <ul class="kz-contact-list">
                <?php if ($cAddress): ?>
                <li><i class="fa fa-map-marker" aria-hidden="true"></i><span><?= e(t("contact_address_l")) ?>:</span> <?= e($cAddress) ?></li>
                <?php endif; ?>
                <?php if ($cPhone): ?>
                <li><i class="fa fa-phone" aria-hidden="true"></i><span><?= e(t("contact_phone_l")) ?>:</span> <a href="tel:<?= e($cPhone) ?>"><?= e($cPhone) ?></a></li>
                <?php endif; ?>
                <?php if ($cEmail): ?>
                <li><i class="fa fa-envelope-o" aria-hidden="true"></i><span><?= e(t("contact_email_l")) ?>:</span> <a href="mailto:<?= e($cEmail) ?>"><?= e($cEmail) ?></a></li>
                <?php endif; ?>
                <?php if ($cWebsite): ?>
                <li><i class="fa fa-globe" aria-hidden="true"></i><span>Website:</span> <a href="<?= e($cWebsite) ?>" target="_blank" rel="noopener"><?= e($cWebsite) ?></a></li>
                <?php endif; ?>
              </ul>
            </div>
          </div>
          <div class="col-xs-12 col-sm-7 col-md-7">
            <div class="title-left title-style04 underline04">
              <h3><?= e(t("find_us_map")) ?></h3>
            </div>
            <!-- Begin Google Map (embed from Admin Settings) -->
            <?php if ($mapEmbed): ?>
              <iframe src="<?= e($mapEmbed) ?>" class="kz-map" style="border:0;width:100%;height:300px;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Map"></iframe>
            <?php else: ?>
              <div class="alert" style="background:#f6f6f6;padding:20px;color:#777;">Map not set yet.</div>
            <?php endif; ?>
            <!-- End Google Map -->
          </div>
        </div>
        <!-- End .contact-us -->
      </div>

      <div class="col-xs-12 col-sm-12 col-md-12">
        <!-- Begin .contact-form -->
        <div class="title-left title-style04 underline04">
          <h3><?= e(t("send_message")) ?></h3>
        </div>
        <form id="contact-form" method="post" action="contact">
          <input type="hidden" name="do" value="contact">
          <input type="text" name="website" class="kz-hp" tabindex="-1" autocomplete="off" aria-hidden="true">
          <div class="controls">
            <div class="row no-gutter">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="form_name"><?= e(t("msg_name")) ?> *</label>
                  <input id="form_name" type="text" name="name" class="form-control" placeholder="<?= e(t("msg_name")) ?> *" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="form_email"><?= e(t("msg_email")) ?> *</label>
                  <input id="form_email" type="email" name="email" class="form-control" placeholder="<?= e(t("msg_email")) ?> *" required>
                </div>
              </div>
            </div>
            <div class="row no-gutter">
              <div class="col-md-12">
                <div class="form-group">
                  <label for="form_phone"><?= e(t("contact_phone_l")) ?></label>
                  <input id="form_phone" type="tel" name="phone" class="form-control" placeholder="<?= e(t("contact_phone_l")) ?>">
                </div>
              </div>
            </div>
            <div class="row no-gutter">
              <div class="form-group">
                <label for="form_message"><?= e(t("msg_body")) ?> *</label>
                <textarea id="form_message" name="message" class="form-control" placeholder="<?= e(t("msg_body")) ?> *" rows="4" required></textarea>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <input type="submit" class="btn btn-success btn-send" value="<?= e(t("send_message")) ?>">
                </div>
              </div>
            </div>
          </div>
        </form>
        <!-- End .contact-form -->
      </div>

    </div>
  </section>
  <!--========== END .MODULE ==========-->
</div>
<!--========== END #MAIN-SECTION ==========-->

<?php require __DIR__ . '/footer.php'; ?>
