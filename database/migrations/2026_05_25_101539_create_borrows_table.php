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
        Schema::create('borrows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            $table->enum('status', [
                'pending',    // طلب بانتظار الاستلام
                'borrowed',   // تم الاستلام
                'returned',   // تم الإرجاع
                'cancelled',  // انتهت 12 ساعة
                'overdue',    // تجاوز 7 أيام
            ])->default('pending');
            $table->timestamp('requested_at');           // وقت الطلب
            $table->timestamp('expires_at');             // انتهاء 12 ساعة
            $table->timestamp('borrowed_at')->nullable(); // وقت الاستلام
            $table->timestamp('due_at')->nullable();      // موعد الإرجاع (7 أيام)
            $table->timestamp('returned_at')->nullable(); // وقت الإرجاع الفعلي
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('borrows');
    }
};
