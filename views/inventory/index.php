<?php page_start('Inventory');
?>
<div class="page-heading">
    <div>
        <h1>Inventory</h1>
        <p>Keep every item accounted for.</p>
    </div>
    <a class="button" href="tambah.php">Add item</a>
</div>

<form class="filters" method="get">
    <div>
        <label for="q">Find an item</label>
        <input
            id="q"
            name="q"
            placeholder="Search name or SKU"
            value="<?php echo e($search); ?>"
        >
    </div>

    <div>
        <label for="category">Category</label>
        <select id="category" name="category">
            <option value="">All categories</option>
            <?php foreach ($categories as $categoryOption): ?>
                <option
                    value="<?php echo e($categoryOption['id']); ?>"
                    <?php echo $category == $categoryOption['id'] ? 'selected' : ''; ?>
                >
                    <?php echo e($categoryOption['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <button class="button secondary">Apply filters</button>
    <a class="text-link" href="index.php">Reset</a>
</form>

<div class="table-heading">
    <h2>Item register</h2>
    <span>
        <?php echo count($items); ?>
        <?php echo count($items) === 1 ? 'item' : 'items'; ?> shown
    </span>
</div>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Item / SKU</th>
                <th>Category</th>
                <th class="numeric">Unit price</th>
                <th class="numeric">On hand</th>
                <th class="string">Unit</th>
                <th>User</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td>
                        <strong><?php echo e($item['name']); ?></strong>
                        <small><?php echo e($item['sku']); ?></small>
                    </td>
                    <td><?php echo e($item['category']); ?></td>
                    <td class="numeric"><?php echo e(money($item['unit_price'])); ?></td>
                    <td class="numeric">
                        <span class="stock <?php echo !$item['stock'] ? 'zero' : ''; ?>">
                            <?php echo e($item['stock']); ?>
                        </span>
                    </td>
                    <td><?php echo e($item['unit']); ?></td>
                    <td><?php echo e($item['user_input']); ?></td>
                    <td>
                        <div class="actions">
                            <a href="edit.php?id=<?php echo e($item['id']); ?>">Edit</a>
                            <form
                                action="hapus.php"
                                method="post"
                                data-confirm="Delete this item? Items with transaction history cannot be deleted."
                            >
                                <?php csrf_field(); ?>
                                <input type="hidden" name="id" value="<?php echo e($item['id']); ?>">
                                <button class="danger-link">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>

            <?php if (!$items): ?>
                <tr>
                    <td colspan="7" class="empty">
                        No items found. Change your filters or
                        <a href="tambah.php">add an item</a>.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<p class="helper">
    Stock changes are recorded in <a href="transaksi.php">Transactions</a>.
    Item edits keep stock intact.
</p>

<?php page_end(); ?>
