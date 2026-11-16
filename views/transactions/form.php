<?php page_start('Record movement', 'transactions');
?>
<a class="text-link" href="transaksi.php">Back to transactions</a>

<div class="page-heading">
    <div>
        <h1>Record movement</h1>
        <p>Receive stock or record a dispatch. Each movement stays in the ledger.</p>
    </div>
</div>

<?php errors_html($errors); ?>

<?php if (!$items): ?>
    <div class="notice">
        Add an item before recording a movement.
        <a href="tambah.php">Add item</a>
    </div>
<?php else: ?>
    <form class="entry-form" method="post">
        <?php csrf_field(); ?>

        <div class="form-grid">
            <div class="full">
                <label for="item_id">Item</label>
                <select required id="item_id" name="item_id">
                    <option value="">Select an item</option>
                    <?php foreach ($items as $item): ?>
                        <?php
                        $itemLabel = $item['sku'] . ' — ' . $item['name']
                            . ' (' . $item['stock'] . ' ' . $item['unit'] . ' available)';
                        ?>
                        <option
                            value="<?php echo e($item['id']); ?>"
                            <?php echo $itemId == $item['id'] ? 'selected' : ''; ?>
                        >
                            <?php echo e($itemLabel); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="type">Movement</label>
                <select required id="type" name="type">
                    <option value="IN" <?php echo $type === 'IN' ? 'selected' : ''; ?>>
                        Incoming — receive stock
                    </option>
                    <option value="OUT" <?php echo $type === 'OUT' ? 'selected' : ''; ?>>
                        Outgoing — dispatch stock
                    </option>
                </select>
            </div>

            <div>
                <label for="quantity">Quantity</label>
                <input
                    required
                    type="number"
                    min="1"
                    max="999999999"
                    step="1"
                    id="quantity"
                    name="quantity"
                    value="<?php echo e($quantity); ?>"
                >
            </div>

            <div>
                <label for="transaction_date">Transaction date</label>
                <input
                    required
                    type="date"
                    id="transaction_date"
                    name="transaction_date"
                    value="<?php echo e($date); ?>"
                >
            </div>

            <div class="full">
                <label for="notes">
                    Notes <span class="muted font-normal">(optional)</span>
                </label>
                <textarea
                    rows="3"
                    maxlength="500"
                    id="notes"
                    name="notes"
                ><?php echo e($notes); ?></textarea>
            </div>
        </div>

        <div class="form-footer">
            <button class="button">Record movement</button>
            <a href="transaksi.php">Cancel</a>
            <span>Recorded movements cannot be edited or deleted.</span>
        </div>
    </form>
<?php endif; ?>

<?php page_end(); ?>
