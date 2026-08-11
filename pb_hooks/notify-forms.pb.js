/// <reference path="../pb_data/types.d.ts" />

onRecordAfterCreateSuccess((e) => {
  const r = e.record;
  const msg = new MailerMessage({
    from: { name: "Ngon Thị Hoa" },
    to: [{ address: "info@ngonthihoarestaurant.com" }],
    subject: "Tin nhắn liên hệ mới từ website",
    html: `
      <h2>Tin nhắn liên hệ mới</h2>
      <p><strong>Họ tên:</strong> ${r.get("full_name")}</p>
      <p><strong>Email:</strong> ${r.get("email")}</p>
      <p><strong>Điện thoại:</strong> ${r.get("phone") || "Không có"}</p>
      <p><strong>Tiêu đề:</strong> ${r.get("subject") || "Không có"}</p>
      <p><strong>Nội dung:</strong></p>
      <p>${(r.get("message") || "").replace(/\n/g, "<br>")}</p>
    `,
  });
  try { $app.newMailClient().send(msg); } catch (err) {
    $app.logger().error("contact email failed", "err", String(err));
  }
  e.next();
}, "contact_messages");

onRecordAfterCreateSuccess((e) => {
  const r = e.record;
  const msg = new MailerMessage({
    from: { name: "Ngon Thị Hoa" },
    to: [{ address: "info@ngonthihoarestaurant.com" }],
    subject: `Đơn ứng tuyển mới - ${r.get("position")}`,
    html: `
      <h2>Đơn ứng tuyển mới</h2>
      <p><strong>Họ tên:</strong> ${r.get("full_name")}</p>
      <p><strong>Email:</strong> ${r.get("email")}</p>
      <p><strong>Điện thoại:</strong> ${r.get("phone")}</p>
      <p><strong>Vị trí ứng tuyển:</strong> ${r.get("position")}</p>
      <p><strong>Thư giới thiệu:</strong></p>
      <p>${(r.get("cover_letter") || "Không có").replace(/\n/g, "<br>")}</p>
    `,
  });
  try { $app.newMailClient().send(msg); } catch (err) {
    $app.logger().error("job application email failed", "err", String(err));
  }
  e.next();
}, "job_applications");

onRecordAfterCreateSuccess((e) => {
  const r = e.record;
  const msg = new MailerMessage({
    from: { name: "Ngon Thị Hoa" },
    to: [{ address: "info@ngonthihoarestaurant.com" }],
    subject: `Đặt bàn mới - ${r.get("reservation_date")} ${r.get("reservation_time")} - ${r.get("guests")} khách`,
    html: `
      <h2>Đặt bàn mới từ website</h2>
      <p><strong>Họ tên:</strong> ${r.get("full_name")}</p>
      <p><strong>Email:</strong> ${r.get("email")}</p>
      <p><strong>Điện thoại:</strong> ${r.get("phone")}</p>
      <p><strong>Ngày:</strong> ${r.get("reservation_date")}</p>
      <p><strong>Giờ:</strong> ${r.get("reservation_time")}</p>
      <p><strong>Số khách:</strong> ${r.get("guests")}</p>
      <p><strong>Ghi chú:</strong> ${(r.get("notes") || "Không có").replace(/\n/g, "<br>")}</p>
    `,
  });
  try { $app.newMailClient().send(msg); } catch (err) {
    $app.logger().error("reservation email failed", "err", String(err));
  }
  e.next();
}, "reservations");
