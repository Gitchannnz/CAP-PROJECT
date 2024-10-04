<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameStockInToStockinQuantityInInventoryTable extends Migration
{
    public function up()
    {
        Schema::table('inventory', function (Blueprint $table) {
            $table->renameColumn('quantity', 'stockin_quantity');
        });
    }

    public function down()
    {
        Schema::table('inventory', function (Blueprint $table) {
            $table->renameColumn('stockin_quantity', 'quantity');
        });
    }
}
