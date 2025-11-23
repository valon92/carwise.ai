<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('mechanics')) {
            Schema::create('mechanics', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->string('name')->nullable();
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->string('address')->nullable();
                $table->string('city')->nullable();
                $table->string('country')->nullable();
                $table->decimal('lat', 10, 7)->nullable();
                $table->decimal('lng', 10, 7)->nullable();
                $table->string('geohash', 16)->nullable()->index();
                $table->json('services')->nullable();
                $table->string('logo_path')->nullable();
                $table->json('gallery')->nullable();
                $table->unsignedTinyInteger('experience_years')->default(0);
                $table->json('expertise')->nullable();
                $table->string('location')->nullable();
                $table->decimal('hourly_rate', 8, 2)->nullable();
                $table->decimal('rating', 3, 2)->default(0);
                $table->unsignedInteger('review_count')->default(0);
                $table->string('availability')->nullable();
                $table->text('bio')->nullable();
                $table->json('certifications')->nullable();
                $table->boolean('is_verified')->default(false)->index();
                $table->timestamps();
                $table->index(['city', 'country']);
            });
            return;
        }

        Schema::table('mechanics', function (Blueprint $table) {
            $table->string('name')->nullable()->after('user_id');
            $table->string('phone')->nullable()->after('name');
            $table->string('email')->nullable()->after('phone');
            $table->string('address')->nullable()->after('email');
            $table->string('city')->nullable()->after('address');
            $table->string('country')->nullable()->after('city');
            $table->decimal('lat', 10, 7)->nullable()->after('country');
            $table->decimal('lng', 10, 7)->nullable()->after('lat');
            $table->string('geohash', 16)->nullable()->after('lng')->index();
            $table->json('services')->nullable()->after('geohash');
            $table->string('logo_path')->nullable()->after('services');
            $table->json('gallery')->nullable()->after('logo_path');
            $table->index(['city', 'country']);
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('mechanics')) {
            return;
        }

        Schema::table('mechanics', function (Blueprint $table) {
            $cols = ['name','phone','email','address','city','country','lat','lng','geohash','services','logo_path','gallery'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('mechanics', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};


