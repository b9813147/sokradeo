<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyTbaPlaylistTracksTableAddFragname extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tba_playlist_tracks', function (Blueprint $table) {
        
            $table->string('frag_name',        64 )->nullable()->after('list_order');
            $table->string('frag_description', 128)->nullable()->after('frag_name' );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (env('APP_ENV', 'production') === 'production') {
            throw new \Exception('no migration rollback in production app env');
            return;
        }
        
        Schema::table('tba_playlist_tracks', function (Blueprint $table) {
        
            $table->dropColumn('frag_name');
            $table->dropColumn('frag_description');
        });
    }
}
