/**
 * Test Project - Script xử lý tương tác giao diện
 */

document.addEventListener("DOMContentLoaded", function () {
  // 1. Tự động ẩn các thông báo (Success/Error) sau 3 giây
  const alerts = document.querySelectorAll(".alert, .message-success, .message-error");
  alerts.forEach((alert) => {
    setTimeout(() => {
      alert.style.transition = "opacity 0.5s ease";
      alert.style.opacity = "0";
      setTimeout(() => alert.remove(), 500);
    }, 3000);
  });

  // 2. Xác nhận khi thực hiện hành động xóa
  const deleteButtons = document.querySelectorAll(".btn-delete, .delete-link");
  deleteButtons.forEach((button) => {
    button.addEventListener("click", function (e) {
      if (!confirm("Bạn có chắc chắn muốn xóa mục này? Hành động này không thể hoàn tác!")) {
        e.preventDefault();
      }
    });
  });

  // 3. Hiệu ứng xem trước ảnh (Preview Image) khi Admin thêm sản phẩm
  const imageInput = document.querySelector('input[name="anh"]');
  const imagePreview = document.querySelector("#preview-image"); // Cần thêm ID này vào thẻ img trong form
  if (imageInput && imagePreview) {
    imageInput.addEventListener("change", function () {
      const file = this.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
          imagePreview.src = e.target.result;
          imagePreview.style.display = "block";
        };
        reader.readAsDataURL(file);
      }
    });
  }

  // 4. Định dạng tiền tệ ngay khi nhập (Real-time Currency Formatting)
  const priceInputs = document.querySelectorAll(".input-price");
  priceInputs.forEach((input) => {
    input.addEventListener("input", function (e) {
      let value = e.target.value.replace(/\D/g, ""); // Chỉ giữ lại số
      e.target.value = new Intl.NumberFormat("vi-VN").format(value);
    });
  });
});

/**
 * Hàm hỗ trợ tính toán nhanh trong giỏ hàng (nếu dùng AJAX sau này)
 */
function updateCartTotal() {
  console.log("Giỏ hàng đã được cập nhật!");
}
