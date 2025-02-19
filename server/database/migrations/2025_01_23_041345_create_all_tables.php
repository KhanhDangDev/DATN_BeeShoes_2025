<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

// use App\Constants;
use App\Constants\SystemConstant; // Hệ thống quy ước chung
use App\Constants\CommonStatus; // Trạng thái chung
use App\Constants\ProductStatus;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tài khoản

        // Khách hàng
        Schema::create('khach_hang', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('ma_khach_hang')->unique();
            $table->string('ten_khach_hang', SystemConstant::DEFAULT_MAX_LENGTH);
            $table->string('email')->unique();
            $table->string('so_dien_thoai')->unique();
            $table->tinyInteger('gioi_tinh')->nullable();
            $table->date('ngay_sinh')->nullable();
            $table->timestamps();
        });
        // Nhân viên
        Schema::create('nhan_vien', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('ma_nhan_vien')->unique();
            $table->string('ten_nhan_vien', SystemConstant::DEFAULT_MAX_LENGTH);
            $table->string('email')->unique();
            $table->string('mat_khau')->nullable();
            $table->string('so_dien_thoai')->unique();
            $table->tinyInteger('gioi_tinh')->nullable();
            $table->date('ngay_sinh')->nullable();
            $table->enum('trang_thai', CommonStatus::CommonStatusArray())->default(CommonStatus::DANG_HOAT_DONG);
            $table->timestamps();
        });
        // Màu sắc.
        Schema::create('mau_sac', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('ma_mau_sac', SystemConstant::CODE_MAX_LENGTH)->unique();
            $table->string('ten_mau_sac', SystemConstant::DEFAULT_MAX_LENGTH);
            $table->enum('trang_thai', CommonStatus::CommonStatusArray())->default(CommonStatus::DANG_HOAT_DONG);
            $table->timestamps();
        });

        // Thương hiệu.
        Schema::create('thuong_hieu', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('ma_thuong_hieu', SystemConstant::CODE_MAX_LENGTH)->unique();
            $table->string('ten_thuong_hieu', SystemConstant::DEFAULT_MAX_LENGTH);
            $table->enum('trang_thai', CommonStatus::CommonStatusArray())->default(CommonStatus::DANG_HOAT_DONG);
            $table->timestamps();
        });


        // Chất liệu.
        Schema::create('chat_lieu', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('ma_chat_lieu', SystemConstant::CODE_MAX_LENGTH)->unique();
            $table->string('ten_chat_lieu', SystemConstant::DEFAULT_MAX_LENGTH);
            $table->enum('trang_thai', CommonStatus::CommonStatusArray())->default(CommonStatus::DANG_HOAT_DONG);
            $table->timestamps();
        });

        // Sản phẩm.
        Schema::create('san_pham', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('ma_san_pham', SystemConstant::CODE_MAX_LENGTH)->unique(); //
            $table->string('ten_san_pham', SystemConstant::DEFAULT_MAX_LENGTH);
            $table->text('mo_ta')->nullable();
            $table->decimal('don_gia', 15, 2)->default(0); // mặc định là 0.
            $table->enum('trang_thai', ProductStatus::ProductStatusArray())->default(ProductStatus::DANG_KINH_DOANH);
            $table->foreignUuid('id_mau_sac')->references('id')->on('mau_sac')->onDelete('cascade');
            $table->foreignUuid('id_thuong_hieu')->references('id')->on('thuong_hieu')->onDelete('cascade');
            $table->foreignUuid('id_chat_lieu')->references('id')->on('chat_lieu')->onDelete('cascade');
            $table->timestamps();
        });

        // Kích cỡ.
        Schema::create('kich_co', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('id_san_pham')->references('id')->on('san_pham')->onDelete('cascade');
            $table->string('ten_kich_co', SystemConstant::DEFAULT_MAX_LENGTH);
            $table->integer('so_luong_ton')->default(0);
            $table->enum('trang_thai', CommonStatus::CommonStatusArray())->default(CommonStatus::DANG_HOAT_DONG);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::table('san_pham', function (Blueprint $table) {
        //     $table->dropForeign(['id_mau_sac']);  // Xóa khóa ngoại 'id_mau_sac'
        //     $table->dropForeign(['id_thuong_hieu']);  // Xóa khóa ngoại 'id_thuong_hieu'
        //     $table->dropForeign(['id_chat_lieu']);  // Xóa khóa ngoại 'id_chat_lieu'
        // });

        Schema::dropIfExists('kich_co');
        Schema::dropIfExists('san_pham');

        // Sau khi các bảng con đã được xóa, bạn có thể xóa các bảng chính
        Schema::dropIfExists('mau_sac');
        Schema::dropIfExists('thuong_hieu');
        Schema::dropIfExists('chat_lieu');
    }
};
