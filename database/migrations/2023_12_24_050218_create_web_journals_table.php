<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('web_journals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("admin_id");
            $table->unsignedBigInteger("web_journal_category_id");
            $table->text('short_title')->nullable();
            $table->text('name')->nullable();
            $table->string('slug',255)->nullable();
            $table->string('image',255)->nullable();
            $table->longText('tags')->nullable();
            $table->longText('details')->nullable();
            $table->boolean('status')->default(1);
            $table->timestamps();

            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('web_journal_category_id')->references('id')->on('web_journal_categories')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('web_journals');
    }
};
