<?php
// This file builds the HTML email body
ob_start();
?>

<div style="max-width:600px;margin:auto;font-family:sans-serif;border:1px solid #eee;border-radius:10px;overflow:hidden;">
  <!-- Header -->
  <div style="background:#000;color:#fff;padding:20px;text-align:center">
    <h1 style="margin:0;font-size:28px;color:#f7e733;">ADA AROMAS</h1>
    <p style="margin:5px 0;color:#ddd;">Order ID: <strong>#<?= $newOrderId ?></strong></p>
  </div>

  <!-- Body -->
  <div style="padding:20px;background:#fafafa;color:#0047ab;">
    <p style="font-size:16px;">Hi <strong><?= $data['user']['name'] ?></strong>,</p>
    <p style="font-size:15px;">We're excited to let you know that your order has been successfully placed and paid via Razorpay.</p>

    <table style="width:100%;border-collapse:collapse;margin-top:15px">
      <thead>
        <tr style="background:#eee;text-align:left;color:#000;">
          <th style="padding:10px;border:1px solid #ddd;">Product</th>
          <th style="padding:10px;border:1px solid #ddd;">Qty</th>
          <th style="padding:10px;border:1px solid #ddd;">Size</th>
          <th style="padding:10px;border:1px solid #ddd;">Price</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($cart as $item): ?>
          <?php $size = isset($item['size']) ? strtoupper($item['size']) : 'N/A'; ?>
          <tr>
            <td style="padding:10px;border:1px solid #ddd;">
              <img src="<?= $item['image'] ?? 'https://via.placeholder.com/60' ?>" alt="<?= $item['title'] ?>" style="width:60px;height:auto;vertical-align:middle;margin-right:8px;border-radius:6px;">
              <?= $item['title'] ?>
            </td>
            <td style="padding:10px;border:1px solid #ddd;"><?= $item['quantity'] ?></td>
            <td style="padding:10px;border:1px solid #ddd;"><?= $size ?></td>
            <td style="padding:10px;border:1px solid #ddd;">₹<?= ($item['price'] * $item['quantity']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="3" style="padding:10px;border:1px solid #ddd;text-align:right;"><strong>Total Product Value:</strong></td>
          <td style="padding:10px;border:1px solid #ddd;">₹<?= number_format($totalASP, 2) ?></td>
        </tr>
        <tr>
          <td colspan="3" style="padding:10px;border:1px solid #ddd;text-align:right;"><strong>GST (18%):</strong></td>
          <td style="padding:10px;border:1px solid #ddd;">₹<?= number_format($gst, 2) ?></td>
        </tr>
        <tr>
          <td colspan="3" style="padding:10px;border:1px solid #ddd;text-align:right;"><strong>Total Paid:</strong></td>
          <td style="padding:10px;border:1px solid #ddd;">₹<?= number_format($total, 2) ?></td>
        </tr>
      </tfoot>
    </table>

    <p style="margin-top:15px;font-size:15px;"><strong>Transaction ID:</strong> <?= $paymentId ?></p>

    <div style="margin-top:25px;text-align:center;">
      <span style="font-size:18px;font-weight:bold;color:#e60073;animation:blinker 1.2s linear infinite;display:inline-block;">
        🎁 Assured FREE GIFT with Your Order!🎁
      </span>
    </div>

    <style>
    @keyframes blinker {
      50% { opacity: 0; }
    }
    </style>

    <div style="text-align:center;margin:30px 0;">
      <a href="<?= $orderLink ?>" style="padding:12px 25px;background:#000;color:#fff;border-radius:6px;text-decoration:none;font-weight:bold;">View Full Order Details</a>
      <p style="margin-top:10px;font-size:14px;color:#555;">
        Use this code to cancel your order if needed: 
        <strong style="color:#d9534f;"><?= $cancelCode ?></strong>
      </p>
    </div>

    <p style="font-size:14px;color:#888;">If you have any questions, email to 
      <a href="mailto:<?= $emailConfig['from_email'] ?>" style="color:#888;text-decoration:underline;"><?= $emailConfig['from_email'] ?></a> 
      and we’ll get back to you shortly.</p>
    <p style="font-size:14px;color:#888;">– Team ADA Aromas</p>
  </div>

  <!-- Footer -->
  <div style="background:#000;color:#fff;padding:15px;text-align:center;font-size:13px">
    Further Order Visit :
    <a href="https://adaaromas.co.in" style="color:#fff;text-decoration:underline;margin:0 5px;">ADA AROMAS</a>
  </div>
</div>

<?php
$mailBody = ob_get_clean();
?>
