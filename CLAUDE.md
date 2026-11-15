# CLAUDE.md — Plugin branch-management

## Plugin này là gì

**Kho hàng chi nhánh** (v3.0.2) — quản lý danh sách chi nhánh/kho (tên, SĐT, email, địa chỉ, tỉnh/phường, trạng thái, chi nhánh mặc định). Bản thân plugin chỉ là danh mục chi nhánh; các plugin khác dùng bảng `branchs` làm khoá ngoại (stock-manager, giaohangnhanh, báo cáo của sicommerce).

- Namespace `BranchManagement\*` (alias `BranchManagement` → `app/`, thêm PSR-4 `Enums`). Main class `BranchManagement` (`index.php`): `active()`/`uninstall()` `include_once` thẳng file Service rồi gọi.
- Alias controller `branch` → `BranchController` (khai trong `plugin.json`).

## Phụ thuộc

- Dùng `Location2` (tỉnh/phường) và chuỗi dịch `sicommerce::checkout.field.city.select` trong form → cần sicommerce có mặt; không guard.
- Bảng `order` do sicommerce tạo; Activator có kiểm `hasTable` nên thiếu thì bỏ qua.
- **Plugin khác đọc bảng này**: `stock-manager` (cột `branch_id` ở tồn kho), `giaohangnhanh`, `sicommerce` (`ReportAjax`, `Reports/Report`). Đổi tên bảng/cột là phá các plugin đó.

## Dữ liệu

- Bảng `branchs`: `name`, `phone`, `email`, `address`, `ward`, `district`, `city` (int), `status` (`working`|`stop`), `isDefault`, `order`, `area` (cast mảng), `created`, `updated`.
- Thêm cột `order.branch_id`; Activator thêm `users.branch_id` (đã sửa tên bảng, xem mục Đã sửa).
- Uninstall: drop bảng `branchs`, drop cột `branch_id` ở `order` và `users`.
- Quyền: `branch_list`, `branch_add`, `branch_edit` (cấp cho root + administrator; nhãn đăng ký vào user-role-editor qua `user_role_editor_group/label`).
- Cache: xoá `branch_*` mỗi khi `saved`.

## Bản đồ file

| File | Trách nhiệm |
|------|-------------|
| `bootstrap/config.php` | tab hệ thống (nhóm `commerce`, chỉ là link `admin.branch`), hook `manage_branch_input` (form), quyền |
| `bootstrap/ajax.php` | `add`, `save`, `status`, `default` |
| `routes/admin.php` | `admin/branch`, `/add`, `/edit/{id}` (`auth:admin`) |
| `app/Models/Branch.php` | model; scope `widthStop` (chỉ lấy `working` nếu chưa lọc status) |
| `app/Enums/BranchStatus.php` | enum trạng thái, nhãn, badge |
| `app/Modules/Admin/Branch/{BranchForm,BranchTable}.php` | form + bảng theo module `branch` |
| `app/Services/{Activator,Deactivator,BranchRole}Service.php` | vòng đời + quyền |
| `views/admin/index.blade.php` | bảng + JS đổi trạng thái / đặt mặc định |

## Hook cho plugin khác

Filter `admin_branch_save_validation` (trả `SKD_Error` để chặn lưu), `columns_db_branch`, `pre_insert_branch_data`, `manage_branch_input`.

## Đã sửa (15/11/2026)

- v3.0.2. `BranchAjax::status` dùng đúng enum `BranchStatus` (trước đó gọi class không tồn tại nên fatal).
- `status` / `default` tìm chi nhánh không qua `widthStop()` nữa nên bật lại được chi nhánh đã ngưng; chi nhánh ngưng không được đặt làm mặc định.
- `default` và `save(isDefault=1)` đổi mặc định trong transaction, bỏ mặc định ở mọi chi nhánh khác (không lọc status) rồi đặt chi nhánh này lên 1 (luôn đúng 1 mặc định).
- `save` lưu thêm email / city / ward (khi có gửi lên).
- `status`, `default` kiểm quyền `branch_edit` (add/save đã có sẵn).
- Activator / Deactivator dùng bảng `users` (trước là `user`); Deactivator dùng `dropIfExists`. Site đã kích hoạt từ trước phải tắt rồi bật lại plugin để có cột `users.branch_id` (plugin không có cơ chế nâng cấp theo phiên bản).
- Còn lại: hook `admin_branch_save_validation` nhận mảng ở `add` nhưng nhận model ở `save`.
