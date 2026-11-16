<?php page_start('Transactions', 'transactions');
?>
<div class="page-heading">
    <div>
        <h1>Transactions</h1>
        <p>A clear record of every receipt and dispatch.</p>
    </div>
    <a class="button" href="transaksi-tambah.php">Record movement</a>
</div>

<?php errors_html($errors); ?>

<form class="filters" method="get">
    <div>
        <label for="from">From date</label>
        <input type="date" id="from" name="from" value="<?php echo e($from); ?>">
    </div>

    <div>
        <label for="to">To date</label>
        <input type="date" id="to" name="to" value="<?php echo e($to); ?>">
    </div>

    <button class="button secondary">Apply dates</button>
    <a class="text-link" href="transaksi.php">Reset</a>
</form>

<div class="table-heading">
    <h2>Movement ledger</h2>
    <span>Newest movement date first</span>
</div>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Date / reference</th>
                <th>Item / SKU</th>
                <th>Movement</th>
                <th class="numeric">Quantity</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($transactions as $transaction): ?>
                <tr>
                    <td>
                        <?php echo e($transaction['transaction_date']); ?>
                        <small>#<?php echo e($transaction['id']); ?></small>
                    </td>
                    <td>
                        <strong><?php echo e($transaction['name']); ?></strong>
                        <small><?php echo e($transaction['sku']); ?></small>
                    </td>
                    <td>
                        <span class="direction <?php echo $transaction['type'] === 'OUT' ? 'out' : ''; ?>">
                            <?php echo $transaction['type'] === 'IN' ? 'Incoming' : 'Outgoing'; ?>
                        </span>
                    </td>
                    <td class="numeric">
                        <?php echo e($transaction['quantity']); ?>
                        <span class="muted"><?php echo e($transaction['unit']); ?></span>
                    </td>
                    <td class="max-w-xs whitespace-normal break-words">
                        <?php echo e($transaction['notes']); ?>
                    </td>
                </tr>
            <?php endforeach; ?>

            <?php if (!$transactions): ?>
                <tr>
                    <td colspan="5" class="empty">
                        <?php echo $errors
                            ? 'Correct the date range to view transactions.'
                            : 'No transactions in this date range.'; ?>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if (!$errors): ?>
    <div class="recap" aria-label="Filtered transaction totals">
        <div>
            <strong><?php echo count($transactions); ?></strong>
            Transactions
        </div>
        <div>
            <strong><?php echo e($incoming); ?></strong>
            Incoming quantity
        </div>
        <div>
            <strong><?php echo e($outgoing); ?></strong>
            Outgoing quantity
        </div>
    </div>
<?php endif; ?>

<p class="helper">
    Totals combine whole-unit quantities across items. Records are permanent;
    record a compensating movement to correct a mistake.
</p>

<?php page_end(); ?>
