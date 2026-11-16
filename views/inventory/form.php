<?php page_start($editing ? 'Edit item' : 'Add item');
?>
<a class="text-link" href="index.php">Back to inventory</a>

<div class="page-heading">
    <div>
        <h1><?php echo $editing ? 'Edit item' : 'Add item'; ?></h1>
        <p>Item details belong here. Stock movements belong in the ledger.</p>
    </div>
</div>

<?php errors_html($errors); ?>

<form class="entry-form" method="post">
    <?php csrf_field(); ?>

    <div class="form-grid">
        <div>
            <label for="sku">SKU</label>
            <input
                required
                maxlength="40"
                id="sku"
                name="sku"
                value="<?php echo e($item['sku']); ?>"
            >
            <p class="field-help">A unique reference, such as PKG-003.</p>
        </div>

        <div>
            <label for="name">Item name</label>
            <input
                required
                maxlength="120"
                id="name"
                name="name"
                value="<?php echo e($item['name']); ?>"
            >
        </div>

        <div>
            <label for="category_id">Category</label>
            <select required id="category_id" name="category_id">
                <option value="">Select category</option>
                <?php foreach ($categories as $category): ?>
                    <option
                        value="<?php echo e($category['id']); ?>"
                        <?php echo $item['category_id'] == $category['id'] ? 'selected' : ''; ?>
                    >
                        <?php echo e($category['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label for="unit">Unit</label>
            <input
                required
                maxlength="24"
                id="unit"
                name="unit"
                placeholder="box, piece, pair…"
                value="<?php echo e($item['unit']); ?>"
            >
        </div>

        <div>
            <label for="unit_price">Unit price (Rp)</label>
            <input
                required
                type="number"
                min="0"
                max="999999999999.99"
                step="0.01"
                id="unit_price"
                name="unit_price"
                value="<?php echo e($item['unit_price']); ?>"
            >
        </div>

        <div>
            <label for="user_input">User</label>
            <input
                required
                type="text"
                id="user_input"
                name="user_input"
                value="<?php echo e($item['user_input']); ?>"
            >
        </div>
    </div>

    <div class="form-footer">
        <button class="button">
            <?php echo $editing ? 'Save changes' : 'Add item'; ?>
        </button>
        <a href="index.php">Cancel</a>
        <span>Stock is managed through transactions.</span>
    </div>
</form>

<?php page_end(); ?>
