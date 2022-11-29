<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('user_type')->comment('1 for Admin, 2 for Teacher, 3 for Student');
            $table->string('name',50);
            $table->string('email')->unique();
            $table->string('address')->nullable();
            $table->string('profile_picture_url')->nullable();
            $table->string('current_school')->nullable();
            $table->string('previous_school')->nullable();
            $table->string('parents_details')->nullable();
            $table->string('assigned_teacher_id')->nullable();
            $table->string('password');
            $table->tinyInteger('status')->default(0)->comment('0 for InActive, 1 for Active');
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
