<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLtiPlatformsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lti_platforms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('tool_id');
            $table->string('name');
            $table->string('issuer');
            $table->string('client_id');
            $table->string('public_keyset_url');
            $table->string('access_token_url')->nullable();
            $table->string('authentication_request_url')->nullable();
            $table->string('authentication_service_provider')->nullable();
            $table->string('authentication_service_url')->nullable();

            $table->timestamps();
            $table->unique(['issuer', 'client_id']);
            $table->foreign('tool_id')->references('id')->on('lti_tools');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lti_platforms');
    }
}
