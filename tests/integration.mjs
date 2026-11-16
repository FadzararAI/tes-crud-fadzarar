// Run with Node 18+: node tests/integration.mjs
// Uses the running application and creates uniquely named test items.
// Test items without history are deleted. One ledger item is preserved by design.
import assert from 'node:assert/strict';

export async function run(base = 'http://127.0.0.1:8056') {
    let cookie = '';
    const results = [];
    async function request(path, data) {
        const options = { headers: { Cookie: cookie }, redirect: 'manual' };
        if (data) {
            options.method = 'POST';
            options.body = new URLSearchParams(data);
        }
        const response = await fetch(base + '/' + path, options);
        const setCookie = response.headers.get('set-cookie');
        if (setCookie) cookie = setCookie.split(';')[0];
        return { status: response.status, text: await response.text() };
    }
    function check(condition, name) {
        assert.ok(condition, name);
        results.push(name);
    }
    async function token(path) {
        const response = await request(path);
        const match = response.text.match(/name="csrf" value="([a-f0-9]+)"/);
        assert.ok(match, 'CSRF token on ' + path);
        return match[1];
    }
    const sku = 'TEST-' + Date.now();
    const csrf = await token('tambah.php');
    const item = { csrf, sku, name: "Test <script>alert(1)</script> O'Reilly", category_id: '1', unit: 'piece', unit_price: '12500.50' };
    let response = await request('tambah.php', item);
    check(response.status === 302, 'Add item');
    response = await request('index.php?q=' + sku);
    check(response.text.includes('&lt;script&gt;') && response.text.includes('O&#039;Reilly'), 'HTML output and quoted strings escaped');
    const id = response.text.match(/edit.php\?id=(\d+)/)[1];
    response = await request('tambah.php', item);
    check(response.text.includes('already in use'), 'Duplicate SKU rejected');
    response = await request('edit.php?id=' + id, { ...item, name: 'Edited test item', stock: '99999' });
    check(response.status === 302, 'Edit descriptive fields');
    response = await request('transaksi-tambah.php');
    check(response.text.includes('0 piece available'), 'New item starts at zero; forged stock ignored');
    const movement = { csrf, item_id: id, type: 'IN', quantity: '10', transaction_date: '2026-11-16', notes: 'Automated verification' };
    response = await request('transaksi-tambah.php', movement);
    check(response.status === 302, 'Incoming transaction');
    response = await request('transaksi-tambah.php', { ...movement, type: 'OUT', quantity: '11' });
    check(response.text.includes('Not enough stock'), 'Insufficient stock rejected');
    response = await request('transaksi-tambah.php', { ...movement, type: 'OUT', quantity: '3' });
    check(response.status === 302, 'Outgoing transaction');
    response = await request('transaksi-tambah.php');
    check(response.text.includes('7 piece available'), 'Stock reflects accepted movements only');
    for (const quantity of ['0', '-1', '1.5', '1 OR 1=1']) {
        response = await request('transaksi-tambah.php', { ...movement, quantity });
        check(response.text.includes('Quantity must be'), 'Reject invalid quantity: ' + quantity);
    }
    response = await request('transaksi-tambah.php', { ...movement, transaction_date: '2026-02-30' });
    check(response.text.includes('valid transaction date'), 'Impossible date rejected');
    response = await request('hapus.php', { csrf, id });
    check(response.status === 302, 'History deletion redirected');
    response = await request('index.php?q=' + sku);
    check(response.text.includes('history must be preserved') && response.text.includes('Edited test item'), 'Item with history cannot be deleted');
    response = await request('transaksi.php?from=2026-11-16&to=2026-11-16');
    check(response.text.includes('Edited test item') && response.text.includes('Incoming quantity'), 'Inclusive date range and recap');
    response = await request('transaksi.php?from=2027-01-01&to=2026-01-01');
    check(response.text.includes('Start date must be'), 'Reversed date range rejected');
    response = await request('transaksi.php?from=2099-01-01');
    check(response.text.includes('No transactions'), 'Empty date filter');
    response = await request('index.php?q=' + encodeURIComponent("' OR 1=1 --"));
    check(response.text.includes('No items found'), 'Search injection treated as text');
    response = await request('edit.php?id=999999999');
    check(response.status === 404, 'Missing item returns 404');
    response = await request('hapus.php');
    check(response.status === 405, 'GET deletion refused');
    response = await request('hapus.php', { csrf: 'invalid', id });
    check(response.status === 403, 'Invalid CSRF rejected');
    response = await request('tambah.php', { ...item, sku: sku + '-DEL', name: 'Disposable test item' });
    check(response.status === 302, 'Add item without history');
    response = await request('index.php?q=' + sku + '-DEL');
    const deleteId = response.text.match(/edit.php\?id=(\d+)/)[1];
    response = await request('hapus.php', { csrf, id: deleteId });
    check(response.status === 302, 'Delete item without history');
    response = await request('index.php?q=' + sku + '-DEL');
    check(response.text.includes('No items found'), 'Deleted item no longer listed');
    return { passed: results.length, results, retainedTestItem: sku };
}

if (typeof process !== 'undefined' && process.argv[1]?.endsWith('integration.mjs')) {
    console.log(await run(process.env.APP_URL));
}
