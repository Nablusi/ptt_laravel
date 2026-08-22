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
        Schema::create('channels', function (Blueprint $table) {
            $table->uuid('id')->primary();
            // account id هون عدلي حسب الاكوانت  اللي هي الشركة 
            //$table->foreignUuid('company_id')->references('id')->on('account')->onDelete('cascade');
            $table->foreignUuid('parent_channel_id')->nullable()->constrained('channels')->onDelete('cascade');
            $table->string('name');
            $table->integer('level')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_at')->useCurrent();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('channels');
    }
};
