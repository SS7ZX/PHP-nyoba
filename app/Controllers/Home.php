<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        $menu = [
            'americano'  => ['name' => 'Americano', 'price' => 17000],
            'espresso'   => ['name' => 'Espresso', 'price' => 20000],
            'cappuccino' => ['name' => 'Cappuccino', 'price' => 28000],
            'latte'      => ['name' => 'Cafe Latte', 'price' => 30000],
            'v60'        => ['name' => 'V60', 'price' => 35000],
        ];

        $errors = [];
        $order = null;
        $customer = '';
        $coffee = 'americano';
        $quantity = '1';
        $payment = '';

        if ($this->request->is('post')) {
            $posted = $this->request->getPost();
            $customer = is_string($posted['customer'] ?? null) ? trim($posted['customer']) : '';
            $coffee = is_string($posted['coffee'] ?? null) ? $posted['coffee'] : '';
            $quantity = is_string($posted['quantity'] ?? null) ? $posted['quantity'] : '';
            $payment = is_string($posted['payment'] ?? null) ? $posted['payment'] : '';

            if (mb_strlen($customer) > 60) {
                $errors[] = 'Nama pelanggan maksimal 60 karakter.';
            }

            if (! isset($menu[$coffee])) {
                $errors[] = 'Pilih menu kopi yang tersedia.';
            }

            if (! ctype_digit($quantity) || (int) $quantity < 1 || (int) $quantity > 100) {
                $errors[] = 'Jumlah harus berupa angka bulat antara 1 sampai 100.';
            }

            if (! ctype_digit($payment) || strlen($payment) > 10) {
                $errors[] = 'Pembayaran harus diisi dengan angka bulat dalam rupiah.';
            }

            if ($errors === []) {
                $quantity = (int) $quantity;
                $payment = (int) $payment;
                $unitPrice = $menu[$coffee]['price'];
                $subtotal = $unitPrice * $quantity;

                if ($quantity >= 10) {
                    $discountPercent = 15;
                } elseif ($quantity >= 5) {
                    $discountPercent = 10;
                } else {
                    $discountPercent = 0;
                }

                $discount = (int) ($subtotal * $discountPercent / 100);
                $total = $subtotal - $discount;

                if ($payment < $total) {
                    $errors[] = 'Pembayaran kurang Rp ' . number_format($total - $payment, 0, ',', '.') . '.';
                } else {
                    $order = [
                        'customer'        => $customer !== '' ? $customer : 'Pelanggan',
                        'coffee'          => $menu[$coffee]['name'],
                        'quantity'        => $quantity,
                        'unitPrice'       => $unitPrice,
                        'subtotal'        => $subtotal,
                        'discountPercent' => $discountPercent,
                        'discount'        => $discount,
                        'total'           => $total,
                        'payment'         => $payment,
                        'change'           => $payment - $total,
                    ];
                }
            }
        }

        return view('welcome_message', [
            'menu' => $menu,
            'customer' => $customer,
            'coffee' => $coffee,
            'quantity' => (string) $quantity,
            'payment' => (string) $payment,
            'errors' => $errors,
            'order'  => $order,
        ]);
    }
}
