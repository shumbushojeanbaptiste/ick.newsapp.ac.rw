<?php
require_once __DIR__ . '/../config/auth.php';
require_can('manage_categories');

$pageTitle  = 'Categories';
$activeMenu = 'categories';
$basePath   = '../';

// ---- Handle create / update / delete (POST) ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'save') {
        $id   = (int) ($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $desc = trim($_POST['description'] ?? '');

        if ($name === '') {
            set_flash('danger', 'Category name is required.');
        } else {
            $slug = slugify($name);
            try {
                if ($id > 0) {
                    $stmt = db()->prepare('UPDATE categories SET name = ?, slug = ?, description = ? WHERE id = ?');
                    $stmt->execute([$name, $slug, $desc ?: null, $id]);
                    set_flash('success', 'Category updated.');
                } else {
                    $stmt = db()->prepare('INSERT INTO categories (name, slug, description) VALUES (?, ?, ?)');
                    $stmt->execute([$name, $slug, $desc ?: null]);
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
       FROM categories c ORDER BY c.name'
)->fetchAll();

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
                                    <th>Description</th>
                                    <th>Articles</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!$categories): ?>
                                    <tr><td colspan="5" class="text-center text-muted py-4">No categories yet.</td></tr>
                                <?php else: foreach ($categories as $c): ?>
                                    <tr>
                                        <td class="fw-medium"><?= e($c['name']) ?></td>
                                        <td><code><?= e($c['slug']) ?></code></td>
                                        <td class="text-muted"><?= e($c['description'] ?? '—') ?></td>
                                        <td><span class="badge bg-light text-body"><?= (int) $c['article_count'] ?></span></td>
                                        <td class="text-end">
                                            <button class="btn btn-sm btn-soft-secondary"
                                                onclick='editCategory(<?= htmlspecialchars(json_encode([
                                                    "id" => (int) $c["id"],
                                                    "name" => $c["name"],
                                                    "description" => $c["description"],
                                                ], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE), ENT_QUOTES) ?>)'>
                                                <i class="ri-pencil-line"></i>
                                            </button>
                                            <button class="btn btn-sm btn-soft-danger"
                                                onclick="confirmDeleteCategory(<?= (int) $c['id'] ?>, <?= htmlspecialchars(json_encode($c['name']), ENT_QUOTES) ?>)">
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
                        <label class="form-label" for="cat-desc">Description</label>
                        <textarea name="description" id="cat-desc" class="form-control" rows="3"></textarea>
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
    function resetCategoryForm() {
        document.getElementById('cat-id').value = 0;
        document.getElementById('cat-name').value = '';
        document.getElementById('cat-desc').value = '';
        document.getElementById('categoryModalTitle').textContent = 'Add Category';
    }
    function editCategory(cat) {
        document.getElementById('cat-id').value = cat.id;
        document.getElementById('cat-name').value = cat.name || '';
        document.getElementById('cat-desc').value = cat.description || '';
        document.getElementById('categoryModalTitle').textContent = 'Edit Category';
        new bootstrap.Modal(document.getElementById('categoryModal')).show();
    }
    function confirmDeleteCategory(id, name) {
        Swal.fire({
            title: 'Delete category?',
            html: 'Articles in <b>' + name + '</b> will become uncategorised.',
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
