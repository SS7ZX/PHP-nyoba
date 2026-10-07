<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Kasir sederhana Kedai Kopi Senja">
    <title>Kedai Kopi Senja</title>
    <style {csp-style-nonce}>
        :root {
            color-scheme: light;
            font-family: Arial, Helvetica, sans-serif;
            color: #33251f;
            background: #f5f1eb;
        }
        * { box-sizing: border-box; }
        body { margin: 0; }
        header {
            padding: 34px 20px;
            color: #fffaf3;
            background: #49352c;
        }
        .wrap { width: min(960px, calc(100% - 32px)); margin: 0 auto; }
        .brand { margin: 0; font-size: 1.8rem; }
        main { padding: 30px 0 48px; }
        .layout { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; align-items: start; }
        .panel {
            padding: 24px;
            border: 1px solid #e6ddd3;
            border-radius: 12px;
            background: #fff;
            box-shadow: 0 5px 18px rgba(55, 39, 29, .05);
        }
        h2 { margin: 0 0 18px; font-size: 1.2rem; }
        .menu-list { display: grid; gap: 10px; margin: 0; padding: 0; list-style: none; }
        .menu-item { display: flex; justify-content: space-between; gap: 12px; padding-bottom: 10px; border-bottom: 1px solid #eee8e1; }
        .menu-item:last-child { border-bottom: 0; }
        .price { white-space: nowrap; font-weight: 700; }
        .hint { margin: 16px 0 0; padding: 12px; border-radius: 8px; background: #f7f1e9; font-size: .92rem; line-height: 1.5; }
        form { display: grid; gap: 14px; }
        label { display: block; margin-bottom: 6px; font-size: .92rem; font-weight: 700; }
        input, select {
            width: 100%;
            min-height: 42px;
            padding: 9px 11px;
            border: 1px solid #cfc4b9;
            border-radius: 7px;
            color: inherit;
            background: #fff;
            font: inherit;
        }
        input:focus, select:focus { outline: 3px solid #ead8c6; border-color: #805d49; }
        button {
            min-height: 44px;
            border: 0;
            border-radius: 7px;
            color: #fff;
            background: #684735;
            font: inherit;
            font-weight: 700;
            cursor: pointer;
        }
        button:hover { background: #49352c; }
        .errors { margin: 0 0 18px; padding: 12px 16px 12px 34px; border-radius: 8px; color: #842029; background: #f8d7da; }
        .receipt { margin-top: 20px; border-color: #d7c2aa; }
        .receipt h2 { margin-bottom: 8px; }
        .receipt-name { margin: 0 0 16px; color: #65564d; }
        .line { display: flex; justify-content: space-between; gap: 16px; margin: 10px 0; }
        .line span:last-child { text-align: right; }
        .total { padding-top: 12px; border-top: 1px solid #e6ddd3; font-size: 1.1rem; font-weight: 700; }
        .change { color: #315d3d; font-size: 1.05rem; font-weight: 700; }
        footer { padding: 18px; color: #75665c; text-align: center; font-size: .85rem; }
        @media (max-width: 700px) {
            .layout { grid-template-columns: 1fr; }
            header { padding: 26px 0; }
            .panel { padding: 20px; }
        }
    </style>
</head>
<body>
<header>
    <div class="wrap">
        <p class="brand">Kedai Kopi Senja</p>
    </div>
</header>

<main class="wrap">
    <?php if ($errors !== []): ?>
        <ul class="errors" role="alert">
            <?php foreach ($errors as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach ?>
        </ul>
    <?php endif ?>

    <div class="layout">
        <section class="panel" aria-labelledby="menu-title">
            <h2 id="menu-title">Menu kopi</h2>
            <ul class="menu-list">
                <?php foreach ($menu as $item): ?>
                    <li class="menu-item">
                        <span><?= esc($item['name']) ?></span>
                        <span class="price">Rp <?= number_format($item['price'], 0, ',', '.') ?></span>
                    </li>
                <?php endforeach ?>
            </ul>
            <p class="hint">
                Beli 1 sampai 4 gelas: harga normal.<br>
                Beli 5 sampai 9 gelas: diskon 10%.<br>
                Beli 10 gelas atau lebih: diskon 15%.
            </p>
        </section>

        <section class="panel" aria-labelledby="order-title">
            <h2 id="order-title">Buat pesanan</h2>
            <form method="post" action="<?= current_url() ?>">
                <?= csrf_field() ?>
                <div>
                    <label for="customer">Nama pelanggan (opsional)</label>
                    <input id="customer" name="customer" type="text" maxlength="60"
                           value="<?= esc($customer) ?>" autocomplete="name">
                </div>
                <div>
                    <label for="coffee">Pilih kopi</label>
                    <select id="coffee" name="coffee" required>
                        <?php foreach ($menu as $key => $item): ?>
                            <option value="<?= esc($key) ?>" <?= $coffee === $key ? 'selected' : '' ?>>
                                <?= esc($item['name']) ?>, Rp <?= number_format($item['price'], 0, ',', '.') ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>
                <div>
                    <label for="quantity">Jumlah gelas</label>
                    <input id="quantity" name="quantity" type="number" min="1" max="100" step="1"
                           value="<?= esc($quantity) ?>" required>
                </div>
                <div>
                    <label for="payment">Uang dibayar (Rp)</label>
                    <input id="payment" name="payment" type="number" min="0" step="1"
                           value="<?= esc($payment) ?>" required>
                </div>
                <button type="submit">Hitung pesanan</button>
            </form>
        </section>
    </div>

    <?php if ($order !== null): ?>
        <section class="panel receipt" aria-labelledby="receipt-title" aria-live="polite">
            <h2 id="receipt-title">Struk pembayaran</h2>
            <p class="receipt-name">Terima kasih, <?= esc($order['customer']) ?>!</p>
            <div class="line"><span>Pesanan</span><span><?= esc($order['coffee']) ?> × <?= $order['quantity'] ?></span></div>
            <div class="line"><span>Harga per gelas</span><span>Rp <?= number_format($order['unitPrice'], 0, ',', '.') ?></span></div>
            <div class="line"><span>Subtotal</span><span>Rp <?= number_format($order['subtotal'], 0, ',', '.') ?></span></div>
            <div class="line">
                <span>Diskon (<?= $order['discountPercent'] ?>%)</span>
                <span>- Rp <?= number_format($order['discount'], 0, ',', '.') ?></span>
            </div>
            <div class="line total"><span>Total</span><span>Rp <?= number_format($order['total'], 0, ',', '.') ?></span></div>
            <div class="line"><span>Dibayar</span><span>Rp <?= number_format($order['payment'], 0, ',', '.') ?></span></div>
            <div class="line change"><span>Kembalian</span><span>Rp <?= number_format($order['change'], 0, ',', '.') ?></span></div>
        </section>
    <?php endif ?>
</main>

<footer>Kedai Kopi Senja &copy; <?= date('Y') ?></footer>
</body>
</html>
