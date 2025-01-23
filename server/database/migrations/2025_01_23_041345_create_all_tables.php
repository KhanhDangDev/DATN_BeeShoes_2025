<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// App\Constants
use App\Constants\SystemConstant; // Hệ thống quy ước chung
use App\Constants\CommonStatus; // Trạng thái chung

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tài khoản

        // Màu sắc.
        Schema::create('mau_sac', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('ma_mau_sac', SystemConstant::CODE_MAX_LENGTH)->unique();
            $table->string('ten_mau_sac', SystemConstant::DEFAULT_MAX_LENGTH);
            $table->date('ngay_tao')->nullable();
            $table->enum('trang_thai', CommonStatus::CommonStatusArray())->default(CommonStatus::DANG_HOAT_DONG);
            $table->timestamps();
        });

        // Thương hiệu.
        Schema::create('thuong_hieu', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('ma_thuong_hieu', SystemConstant::CODE_MAX_LENGTH)->unique();
            $table->string('ten_thuong_hieu', SystemConstant::DEFAULT_MAX_LENGTH);
            $table->date('ngay_tao')->nullable();
            $table->enum('trang_thai', CommonStatus::CommonStatusArray())->default(CommonStatus::DANG_HOAT_DONG);
            $table->timestamps();
        });

        // Kích cỡ.
        Schema::create('kich_co', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('ma_kich_co', SystemConstant::CODE_MAX_LENGTH)->unique();
            $table->string('ten_kich_co', SystemConstant::DEFAULT_MAX_LENGTH);
            $table->date('ngay_tao')->nullable();
            $table->enum('trang_thai', CommonStatus::CommonStatusArray())->default(CommonStatus::DANG_HOAT_DONG);
            $table->timestamps();
        });

        // Chất liệu.
        Schema::create('chat_lieu', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('ma_chat_lieu', SystemConstant::CODE_MAX_LENGTH)->unique();
            $table->string('ten_chat_lieu', SystemConstant::DEFAULT_MAX_LENGTH);
            $table->date('ngay_tao')->nullable();
            $table->enum('trang_thai', CommonStatus::CommonStatusArray())->default(CommonStatus::DANG_HOAT_DONG);
            $table->timestamps();
        });

        // Sản phẩm.
        Schema::create('san_pham', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('ma_san_pham', SystemConstant::CODE_MAX_LENGTH)->unique(); //
            $table->string('ten_san_pham', SystemConstant::DEFAULT_MAX_LENGTH);
            $table->text('mo_ta')->nullable();
            $table->date('ngay_tao')->nullable();
            $table->decimal('don_gia', 15, 2)->default(0); // mặc định là 0.
            $table->enum('trang_thai', CommonStatus::CommonStatusArray())->default(CommonStatus::DANG_HOAT_DONG);
            $table->foreignUuid('id_mau_sac')->references('id')->on('mau_sac');
            $table->foreignUuid('id_thuong_hieu')->references('id')->on('thuong_hieu');
            $table->foreignUuid('id_kich_co')->references('id')->on('kich_co');
            $table->foreignUuid('id_chat_lieu')->references('id')->on('chat_lieu');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mau_sac');
        Schema::dropIfExists('thuong_hieu');
        Schema::dropIfExists('kich_co');
        Schema::dropIfExists('chat_lieu');
        Schema::dropIfExists('san_pham');
    }
};
