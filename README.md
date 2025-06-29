# Pi Node Ranking - PHP Version by Pi2Team

Ứng dụng PHP để kiểm tra xếp hạng Pi Node với giao diện đẹp và tính năng đầy đủ. Được phát triển bởi **Pi2Team**.

## 👥 Về Pi2Team

**Pi2Team** là nhóm phát triển chuyên về các ứng dụng liên quan đến Pi Network và blockchain. Chúng tôi cam kết tạo ra những công cụ hữu ích và dễ sử dụng cho cộng đồng Pi Network.

## ✨ Tính năng

- ✅ Hiển thị danh sách Top 10 Pi Nodes
- ✅ Xem tất cả nodes với phân trang (20 nodes/trang)
- ✅ Tìm kiếm node theo Public Key
- ✅ Copy Public Key vào clipboard
- ✅ Giao diện responsive, đẹp mắt
- ✅ Hoạt động trên hosting PHP
- ✅ SEO friendly với meta tags đầy đủ
- ✅ Branding Pi2Team

## 🚀 Cài đặt

### 1. Upload files lên hosting:
Upload tất cả files vào thư mục public_html hoặc www:
- Đảm bảo file `index.php` ở thư mục gốc
- Tạo thư mục `data/` và upload file `nodes_ranking.json`

### 2. Cấu trúc thư mục:
```
/
├── index.php              # File chính
├── config.php             # Cấu hình
├── .htaccess             # Cấu hình Apache
├── data/
│   └── nodes_ranking.json # Dữ liệu nodes
└── README.md             # Hướng dẫn
```

### 3. Yêu cầu hosting:
- PHP 7.0 trở lên
- Apache với mod_rewrite
- Hỗ trợ file_get_contents()
- Hỗ trợ JSON functions

## 📊 Cập nhật dữ liệu

Để cập nhật danh sách nodes:

1. Chỉnh sửa file `data/nodes_ranking.json`
2. Hoặc thay thế bằng file JSON mới có cùng cấu trúc
3. Cấu trúc JSON:
   ```json
   {
     "xếp hạng": [
       {
         "public_key": "GD3TEKP5DUPS4C2NKZD44HNVLTXJML64JSMQF537XEZDVQPVWNFUT7A4",
         "last_active_date": "2025-06-26T00:00:00.000Z",
         "rank": 1
       }
     ],
     "total_pages": 1,
     "last_updated_at": "2025-01-27T10:30:00.000Z"
   }
   ```

## 🎯 Tính năng chính

### 1. Trang chủ
- Hiển thị thống kê tổng quan
- Top 10 nodes hàng đầu
- Tìm kiếm nhanh
- Branding Pi2Team

### 2. Tìm kiếm
- Tìm kiếm chính xác theo Public Key
- Tìm kiếm gần đúng (partial match)
- Hiển thị kết quả với thông tin chi tiết

### 3. Danh sách tất cả
- Phân trang 20 nodes/trang
- Điều hướng trang linh hoạt
- Nhảy nhanh đến trang bất kỳ

### 4. Giao diện
- Responsive design
- Dark theme với gradient đẹp mắt
- Animations và hover effects
- Copy to clipboard
- SEO optimized

## ⚙️ Customization

### Thay đổi số nodes/trang
Sửa biến `$itemsPerPage` trong `index.php`:
```php
$itemsPerPage = 20; // Thay đổi số này
```

### Thay đổi màu sắc
Chỉnh sửa các class Tailwind CSS trong file `index.php`

### Thêm tính năng
- Thêm API endpoints
- Tích hợp database
- Thêm authentication

## 🔧 Hỗ trợ

Nếu gặp vấn đề:
1. Kiểm tra PHP version >= 7.0
2. Đảm bảo file JSON có cấu trúc đúng
3. Kiểm tra quyền đọc file trên hosting
4. Xem PHP error logs

## 📞 Liên hệ Pi2Team

- **Website**: [Đang cập nhật]
- **Email**: [Đang cập nhật]
- **Telegram**: [Đang cập nhật]

## 📄 License

MIT License - Sử dụng tự do cho mục đích cá nhân và thương mại.

---

**Phát triển bởi Pi2Team** - Chuyên gia về Pi Network và Blockchain Applications