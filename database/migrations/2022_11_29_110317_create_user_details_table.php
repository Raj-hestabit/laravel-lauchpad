<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_details', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->tinyInteger('user_type')->comment('1 for Admin, 2 for Teacher, 3 for Student');
            $table->string('address')->nullable();
            $table->string('profile_picture_url')->nullable();
            $table->string('current_school')->nullable();
            $table->string('previous_school')->nullable();
            $table->string('parents_details')->nullable();
            $table->string('assigned_teacher_id')->nullable();
            $table->tinyInteger('status')->default(0)->comment('0 for InActive, 1 for Active');
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
        Schema::dropIfExists('user_details');
    }
}
