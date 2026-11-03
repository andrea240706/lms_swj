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
        Schema::table('materials', function (Blueprint $table) {
            $table->foreignId('meeting_id')->nullable()->after('id')->constrained('meetings')->nullOnDelete();
            $table->longText('content_html')->nullable()->after('description');
            $table->string('link_url', 2048)->nullable()->after('content_html');
            $table->timestamp('available_from')->nullable()->after('is_published');
            $table->timestamp('available_until')->nullable()->after('available_from');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->dropForeign(['meeting_id']);
            $table->dropColumn([
                'meeting_id',
                'content_html',
                'link_url',
                'available_from',
                'available_until',
            ]);
        });
    }
};
