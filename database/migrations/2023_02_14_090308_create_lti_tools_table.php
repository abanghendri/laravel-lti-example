<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLtiToolsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lti_tools', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('kid');
            $table->string('client_id')->nullable();
            $table->string('name');
            $table->string('issuer');
            $table->string('description')->nullable();
            $table->string('public_keyset_url');
            $table->string('access_token_url')->nullable();
            $table->string('launch_url')->nullable();
            $table->string('deep_linking_url')->nullable();
            $table->string('content-selection_url')->nullable();
            $table->string('authentication_url')->nullable();
            $table->string('authentication_service_url')->nullable();
            $table->string('authentication_service_provider')->nullable();
            $table->tinyInteger('type')
                  ->default(1)
                  ->comment('1 = internal, 2=eksternal');
            $table->string('icon_url')->nullable();
            $table->string('secure_icon_url')->nullable();
            $table->tinyInteger('ags_service')
                  ->default(0)
                  ->comment('Assignment and Grading service,
                    0 to not to use this service,
                    1 use for grade sync,
                    2 for grade and column management');
            $table->tinyInteger('nrp_service')
                  ->default(0)
                  ->comment('use Name and role provisioning service?');
            $table->tinyInteger('tool_setting_service')
                  ->default(0)
                  ->comment('use Tool setting service?');
            $table->tinyInteger('deep_linking_service')
                  ->default(0)
                  ->comment('use Deep Linking service?');
            $table->string('custom_properties')->nullable();

            $table->unique(['kid', 'client_id', 'issuer']);
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
        Schema::dropIfExists('lti_tools');
    }
}
