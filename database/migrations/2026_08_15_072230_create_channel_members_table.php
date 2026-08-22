<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('channel_members', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('channel_id')->constrained('channels')->onDelete('cascade');

            // حراء  هاد جدول اليوز عالمه كومنت انتي شوفي كيف العلاقة راح تكون وعندلي ع هاد الاساس
            //$table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            // channlel roles هون التاشنل كيف لازم يكونوا
            $table->enum('channel_role', ['company_admin', 'channel_admin', 'member'])->default('member');
            $table->boolean('is_muted')->default(false);
            $table->timestamp('joined_at')->useCurrent();

            $table->unique(['channel_id', 'user_id'], 'unique_member');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('channel_members');
    }
};
