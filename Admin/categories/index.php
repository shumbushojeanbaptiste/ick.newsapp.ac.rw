<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/articles.php';   // category_is_descendant(), build_category_options()
require_can('manage_categories');

$pageTitle  = 'Categories';
$activeMenu = 'categories';
$basePath   = '../';

// ---- Handle create / update / delete (POST) ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'save') {
        $id     = (int) ($_POST['id'] ?? 0);
        $name   = trim($_POST['name'] ?? '');
        $desc   = trim($_POST['description'] ?? '');
        $parent = ($_POST['parent_id'] ?? '') !== '' ? (int) $_POST['parent_id'] : null;
        $active = !empty($_POST['is_active']) ? 1 : 0;

        // A category cannot be its own parent.
        if ($parent !== null && $parent === $id) {
            $parent = null;
        }
        // Prevent choosing one of its own descendants as parent (would orphan the tree).
        if ($parent !== null && $id > 0 && category_is_descendant($parent, $id)) {
            set_flash('danger', 'A category cannot be moved under one of its own sub-categories.');
            redirect('categories/index.php');
        }

        if ($name === '') {
            set_flash('danger', 'Category name is required.');
        } else {
            $slug = slugify($name);
            try {
                if ($id > 0) {
                    $stmt = db()->prepare('UPDATE categories SET name = ?, slug = ?, description = ?, parent_id = ?, is_active = ? WHERE id = ?');
                    $stmt->execute([$name, $slug, $desc ?: null, $parent, $active, $id]);
                    set_flash('success', 'Category updated.');
                } else {
                    $stmt = db()->prepare('INSERT INTO categories (name, slug, description, parent_id, is_active) VALUES (?, ?, ?, ?, ?)');
                    $stmt->execute([$name, $slug, $desc ?: null, $parent, $active]);
                    set_flash('success', 'Category created.');
                }
            } catch (PDOException $e) {
                set_flash('danger', 'Could not save (a category with that name may already exist).');
            }
        }
    } elseif ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        db()->prepare('DELETE FROM categories WHERE id = ?')->execute([$id]);
        set_flash('success', 'Category deleted.');
    }
    redirect('categories/index.php');
}

$categories = db()->query(
    'SELECT c.*, (SELECT COUNT(*) FROM articles a WHERE a.category_id = c.id) AS article_count
       FROM categories c ORDER BY c.sort_order, c.name'
)->fetchAll();

// Index by id, and order as a parent -> child tree for display.
$catById = [];
foreach ($categories as $c) {
    $catById[(int) $c['id']] = $c;
}
$tree = build_category_options($categories);   // [['id','name','depth'], ...]

require_once __DIR__ . '/../header.php';
?>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            <div class="row mb-3 align-items-center">
                <div class="col">
                    <h4 class="mb-0">Categories</h4>
                    <p class="text-muted mb-0">Organise news articles into topics.</p>
                </div>
                <div class="col-auto">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#categoryModal"
                            onclick="resetCategoryForm()">
                        <i class="ri-add-line align-bottom me-1"></i> Add Category
                    </button>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive table-card">
                        <table class="table table-centered align-middle table-nowrap mb-0">
                            <thead class="table-light text-muted">
                                <tr>
                                    <th>Name</th>
                                    <th>Slug</th>
                                    <th>Articles</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!$tree): ?>
                                    <tr><td colspan="5" class="text-center text-muted py-4">No categories yet.</td></tr>
                                <?php else: foreach ($tree as $node):
                                    $c = $catById[$node['id']];
                                    $depth = $node['depth']; ?>
                                    <tr<?= empty($c['is_active']) ? ' class="opacity-50"' : '' ?>>
                                        <td class="fw-medium">
                                            <?php if ($depth): ?>
                                                <span class="text-muted"><?= str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $depth) ?>&#8627;</span>
                                            <?php endif; ?>
                                            <?= e($c['name']) ?>
                                        </td>
                                        <td><code><?= e($c['slug']) ?></code></td>
                                        <td><span class="badge bg-light text-body"><?= (int) $c['article_count'] ?></span></td>
                                        <td>
                                            <?php if (!empty($c['is_active'])): ?>
                                                <span class="badge bg-success-subtle text-success">Active</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary-subtle text-secondary">Hidden</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end">
                                            <button class="btn btn-sm btn-soft-secondary"
                                                onclick='editCategory(<?= htmlspecialchars(json_encode([
                                                    "id" => (int) $c["id"],
                                                    "name" => $c["name"],
                                                    "description" => $c["description"],
                                                    "parent_id" => $c["parent_id"] !== null ? (int) $c["parent_id"] : "",
                                                    "is_active" => (int) $c["is_active"],
                                                ], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE), ENT_QUOTES) ?>)'>
                                                <i class="ri-pencil-line"></i>
                                            </button>
                                            <button class="btn btn-sm btn-soft-danger"
                                                onclick="confirmDeleteCategory(<?= (int) $c['id'] ?>, <?= htmlspecialchars(json_encode($c['name']), ENT_QUOTES) ?>, <?= (int) $c['article_count'] ?>)">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <footer class="footer">
        <div class="container-fluid">
            <div class="row"><div class="col-sm-6"><script>document.write(new Date().getFullYear())</script> © <?= e(APP_NAME) ?>.</div></div>
        </div>
    </footer>
</div>

<!-- Add / Edit modal -->
<div class="modal fade" id="categoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="save">
                <input type="hidden" name="id" id="cat-id" value="0">
                <div class="modal-header">
                    <h5 class="modal-title" id="categoryModalTitle">Add Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" for="cat-name">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="cat-name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="cat-parent">Parent section</label>
                        <select name="parent_id" id="cat-parent" class="form-select">
                            <option value="">— None (top-level section) —</option>
                            <?php foreach ($tree as $node): ?>
                                <option value="<?= (int) $node['id'] ?>">
                                    <?= str_repeat('&nbsp;&nbsp;&nbsp;', $node['depth']) . ($node['depth'] ? '— ' : '') . e($node['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted">Leave “None” for a main section; choose a parent to nest it as a sub-section.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="cat-desc">Description</label>
                        <textarea name="description" id="cat-desc" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="cat-active" name="is_active" value="1" checked>
                        <label class="form-check-label" for="cat-active">Active (shown in site menus)</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Hidden delete form, submitted by SweetAlert confirm -->
<form method="post" id="deleteCategoryForm" class="d-none">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="id" id="del-cat-id">
</form>

<?php
$pageScripts = <<<'HTML'
<script>
    // Disable a given option in the parent select (so a category can't be
    // its own parent). Pass 0 to enable all.
    function lockParentOption(disabledId) {
        var sel = document.getElementById('cat-parent');
        for (var i = 0; i < sel.options.length; i++) {
            sel.options[i].disabled = (disabledId && parseInt(sel.options[i].value, 10) === disabledId);
        }
    }
    function resetCategoryForm() {
        document.getElementById('cat-id').value = 0;
        document.getElementById('cat-name').value = '';
        document.getElementById('cat-desc').value = '';
        document.getElementById('cat-parent').value = '';
        document.getElementById('cat-active').checked = true;
        lockParentOption(0);
        document.getElementById('categoryModalTitle').textContent = 'Add Category';
    }
    function editCategory(cat) {
        document.getElementById('cat-id').value = cat.id;
        document.getElementById('cat-name').value = cat.name || '';
        document.getElementById('cat-desc').value = cat.description || '';
        document.getElementById('cat-parent').value = (cat.parent_id || cat.parent_id === 0) ? cat.parent_id : '';
        document.getElementById('cat-active').checked = cat.is_active != 0;
        lockParentOption(cat.id);
        document.getElementById('categoryModalTitle').textContent = 'Edit Category';
        new bootstrap.Modal(document.getElementById('categoryModal')).show();
    }
    function confirmDeleteCategory(id, name, articleCount) {
        var warn = 'The <b>' + name + '</b> section will be removed.';
        warn += '<br>Any sub-categories under it will also be deleted, and ';
        warn += (articleCount > 0 ? ('its ' + articleCount + ' article(s)') : 'any articles');
        warn += ' will become uncategorised.';
        Swal.fire({
            title: 'Delete category?',
            html: warn,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f06548',
            cancelButtonColor: '#74788d',
            confirmButtonText: 'Yes, delete it'
        }).then(function (result) {
            if (result.isConfirmed) {
                document.getElementById('del-cat-id').value = id;
                document.getElementById('deleteCategoryForm').submit();
            }
        });
    }
</script>
HTML;

require_once __DIR__ . '/../footer.php';
?>
