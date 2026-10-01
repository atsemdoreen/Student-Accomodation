<?php
require 'config.php';
$title = 'Gallery';
$active = 'gallery';

$cats = [
  'rooms' => 'Rooms',
  'beds' => 'Beds',
  'kitchen' => 'Kitchen',
  'washrooms' => 'Washrooms',
  'study' => 'Study rooms',
  'lockers' => 'Lockers',
  'environment' => 'Environment',
];

// Caption and category for each file in assets/img/gallery/ (file name without extension).
$photo_meta = [
  '18' => ['rooms', 'Single room'],
  '28' => ['rooms', 'Twin share room'],
  '58' => ['rooms', 'En-suite room'],
  '68' => ['rooms', 'Studio with kitchenette'],
  '78' => ['beds', 'Single bed and desk'],
  '88' => ['beds', 'Bunk beds in the four-bed dorm'],
  '98' => ['kitchen', 'Shared kitchen'],
];

$photos = [];
$seen = [];
$gallery_dir = __DIR__ . '/assets/img/gallery';
if (is_dir($gallery_dir)) {
  foreach (scandir($gallery_dir) as $file) {
    $path = "$gallery_dir/$file";
    if (!is_file($path)) continue;
    $info = pathinfo($file);
    if (!in_array(strtolower($info['extension'] ?? ''), ['jpg', 'jpeg', 'png', 'webp', 'gif', 'avif'], true)) continue;
    $stem = preg_replace('/[\s_]+/', '-', strtolower($info['filename']));
    $src = 'assets/img/gallery/' . rawurlencode($file);
    if (isset($seen[$src])) continue;
    $seen[$src] = true;
    [$cat, $caption] = $photo_meta[$stem] ?? ['rooms', ucwords(str_replace('-', ' ', $stem))];
    $photos[] = [$cat, $caption, $src];
  }
}

$active_cats = [];
foreach ($photos as [$cat]) {
  $active_cats[$cat] = true;
}

include 'includes/header.php';
?>
<section class="wrap page">
  <h1>Gallery</h1>
  <p class="lead">Take a look at the rooms, beds, kitchens, washrooms, study spaces, lockers and the grounds before you book.</p>

  <?php if ($photos): ?>
  <div class="filters" role="group" aria-label="Filter photos">
    <button class="chip on" data-gfilter="all">All</button>
    <?php foreach ($cats as $key => $label): if (empty($active_cats[$key])) continue; ?>
      <button class="chip" data-gfilter="<?= e($key) ?>"><?= e($label) ?></button>
    <?php endforeach; ?>
  </div>

  <div class="gallery">
    <?php foreach ($photos as [$cat, $caption, $src]): ?>
      <figure class="shot t-<?= e($cat) ?>" data-cat="<?= e($cat) ?>">
        <button type="button" class="shot-open" data-full="<?= e($src) ?>" data-caption="<?= e($caption) ?>" aria-label="View larger: <?= e($caption) ?>">
          <img src="<?= e($src) ?>" alt="<?= e($caption) ?>" loading="lazy">
        </button>
        <figcaption><?= e($caption) ?> <span class="muted">(<?= e($cats[$cat]) ?>)</span></figcaption>
      </figure>
    <?php endforeach; ?>
  </div>
  <?php else: ?>
  <p class="muted">Photos will appear here once you add images to <code>assets/img/gallery/</code>.</p>
  <?php endif; ?>
</section>

<?php if ($photos): ?>
<dialog id="lightbox" aria-label="Photo viewer">
  <form method="dialog"><button class="lb-close" aria-label="Close">Close</button></form>
  <img id="lb-img" alt="">
  <p id="lb-cap"></p>
  <div class="lb-nav"><button type="button" id="lb-prev">Previous</button><button type="button" id="lb-next">Next</button></div>
</dialog>
<?php endif; ?>
<?php include 'includes/footer.php'; ?>
