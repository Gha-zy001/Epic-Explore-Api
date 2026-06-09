<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('place_id');
            $table->index('created_at');
            $table->unique(['user_id', 'place_id', 'created_at'], 'visits_user_place_day_unique');
        });

        Schema::table('favorites', function (Blueprint $table) {
            $table->index('user_id');
            $table->index(['favoritable_id', 'favoritable_type']);
            $table->unique(['user_id', 'favoritable_id', 'favoritable_type'], 'favorites_unique');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->index('user_id');
            $table->index(['reviewable_id', 'reviewable_type']);
        });

        Schema::table('reward_logs', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('created_at');
        });

        Schema::table('otps', function (Blueprint $table) {
            $table->index('identifier');
        });

        Schema::table('places', function (Blueprint $table) {
            $table->index('state_id');
            $table->index('name');
        });

        Schema::table('hotels', function (Blueprint $table) {
            $table->index('state_id');
            $table->index('name');
        });

        Schema::table('restaurants', function (Blueprint $table) {
            $table->index('state_id');
        });

        Schema::table('banks', function (Blueprint $table) {
            $table->index('state_id');
        });
    }

    public function down(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['place_id']);
            $table->dropIndex(['created_at']);
            $table->dropUnique('visits_user_place_day_unique');
        });

        Schema::table('favorites', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['favoritable_id', 'favoritable_type']);
            $table->dropUnique('favorites_unique');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['reviewable_id', 'reviewable_type']);
        });

        Schema::table('reward_logs', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('otps', function (Blueprint $table) {
            $table->dropIndex(['identifier']);
        });

        Schema::table('places', function (Blueprint $table) {
            $table->dropIndex(['state_id']);
            $table->dropIndex(['name']);
        });

        Schema::table('hotels', function (Blueprint $table) {
            $table->dropIndex(['state_id']);
            $table->dropIndex(['name']);
        });

        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropIndex(['state_id']);
        });

        Schema::table('banks', function (Blueprint $table) {
            $table->dropIndex(['state_id']);
        });
    }
};
