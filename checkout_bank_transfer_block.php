<?php
require_once __DIR__ . '/config/payment_config.php';
?>

<div class="payment-method">
    <label class="payment-option">
        <input
            type="radio"
            name="payment_method"
            value="COD"
            <?= (($paymentMethod ?? 'COD') === 'COD') ? 'checked' : '' ?>
        >
        <span>💵 Thanh toán khi nhận hàng (COD)</span>
    </label>

    <label class="payment-option">
        <input
            type="radio"
            name="payment_method"
            value="BANK_TRANSFER"
            <?= (($paymentMethod ?? '') === 'BANK_TRANSFER') ? 'checked' : '' ?>
        >
        <span>🏦 Chuyển khoản ngân hàng</span>
    </label>

    <div
        class="bank-info"
        id="bankTransferInfo"
    >
        <h3>🏦 Thông tin chuyển khoản</h3>

        <div>
            <span>Ngân hàng</span>
            <strong><?= htmlspecialchars(BANK_NAME, ENT_QUOTES, 'UTF-8') ?></strong>
        </div>

        <div>
            <span>Số tài khoản</span>
            <strong><?= htmlspecialchars(BANK_ACCOUNT_NUMBER, ENT_QUOTES, 'UTF-8') ?></strong>
        </div>

        <div>
            <span>Chủ tài khoản</span>
            <strong><?= htmlspecialchars(BANK_ACCOUNT_NAME, ENT_QUOTES, 'UTF-8') ?></strong>
        </div>

        <div class="transfer-note">
            💡 <?= htmlspecialchars(BANK_TRANSFER_NOTE, ENT_QUOTES, 'UTF-8') ?>
        </div>
    </div>
</div>

<script>
(function () {
    const radios = document.querySelectorAll(
        'input[name="payment_method"]'
    );

    const bankInfo = document.getElementById(
        'bankTransferInfo'
    );

    function syncBankInfo() {
        const selected = document.querySelector(
            'input[name="payment_method"]:checked'
        );

        if (bankInfo) {
            bankInfo.style.display =
                selected && selected.value === 'BANK_TRANSFER'
                    ? 'block'
                    : 'none';
        }
    }

    radios.forEach(function (radio) {
        radio.addEventListener('change', syncBankInfo);
    });

    syncBankInfo();
})();
</script>
