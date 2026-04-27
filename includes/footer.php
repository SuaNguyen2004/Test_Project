<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
  .main-footer {
    background-color: #2c3e50;
    color: #ecf0f1;
    padding: 50px 0 20px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    margin-top: 50px;
  }

  .footer-container {
    max-width: 1200px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 30px;
    padding: 0 20px;
  }

  .footer-column h3 {
    color: #e74c3c;
    /* Màu đỏ Genius */
    font-size: 1.2rem;
    margin-bottom: 20px;
    text-transform: uppercase;
    position: relative;
  }

  .footer-column h3::after {
    content: '';
    width: 50px;
    height: 2px;
    background: #e74c3c;
    position: absolute;
    bottom: -8px;
    left: 0;
  }

  .footer-column p,
  .footer-column li {
    font-size: 0.95rem;
    line-height: 1.8;
    color: #bdc3c7;
  }

  .footer-column ul {
    list-style: none;
    padding: 0;
  }

  .footer-column ul li a {
    color: #bdc3c7;
    text-decoration: none;
    transition: 0.3s;
  }

  .footer-column ul li a:hover {
    color: #e74c3c;
    padding-left: 5px;
  }

  .contact-info li {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
  }

  .contact-info i {
    color: #e74c3c;
    width: 20px;
  }

  .footer-bottom {
    text-align: center;
    padding-top: 30px;
    margin-top: 40px;
    border-top: 1px solid #3e4f5f;
    font-size: 0.9rem;
    color: #7f8c8d;
  }

  /* Responsive cho mobile */
  @media (max-width: 768px) {
    .footer-container {
      grid-template-columns: 1fr;
      text-align: center;
    }

    .footer-column h3::after {
      left: 50%;
      transform: translateX(-50%);
    }

    .contact-info li {
      justify-content: center;
    }
  }
</style>

<footer class="main-footer">
  <div class="footer-container">
    <div class="footer-column">
      <h3>Về chúng tôi</h3>
      <p>
        <strong>Hệ thống GENIUS</strong> - Chuyên cung cấp các thiết bị công nghệ,
        linh kiện máy tính và giải pháp phần mềm hàng đầu. Chúng tôi cam kết
        mang đến sản phẩm chất lượng và trải nghiệm mua sắm tốt nhất.
      </p>
    </div>

    <div class="footer-column">
      <h3>Hỗ trợ khách hàng</h3>
      <ul>
        <li><a href="#">Hướng dẫn mua hàng</a></li>
        <li><a href="#">Chính sách bảo hành</a></li>
        <li><a href="#">Chính sách đổi trả</a></li>
        <li><a href="#">Phương thức thanh toán</a></li>
        <li><a href="#">Câu hỏi thường gặp (FAQs)</a></li>
      </ul>
    </div>

    <div class="footer-column">
      <h3>Thông tin liên hệ</h3>
      <ul class="contact-info">
        <li>
          <i class="fas fa-map-marker-alt"></i>
          Số 123, Đường ABC, Quận X, TP. Hồ Chí Minh
        </li>
        <li>
          <i class="fas fa-phone-alt"></i>
          Hotline: 090x xxx xxx (8h00 - 21h00)
        </li>
        <li>
          <i class="fas fa-envelope"></i>
          Email: hotro@genius.com
        </li>
        <li>
          <i class="fas fa-globe"></i>
          Website: www.genius-store.com
        </li>
      </ul>
    </div>
  </div>

  <div class="footer-bottom">
    <p>&copy; <?php echo date('Y'); ?> Genius Store. Tất cả quyền được bảo lưu. Thiết kế bởi Genius Team.</p>
  </div>
</footer>