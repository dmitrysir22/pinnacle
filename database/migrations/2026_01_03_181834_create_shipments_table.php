<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
// database/migrations/xxxx_xx_xx_create_shipments_table.php

public function up(): void
{
	
/*	Schema::create('agents', function (Blueprint $table) {
        $table->id();
        $table->string('name'); // Название компании
        $table->string('country_code', 2)->nullable(); // Например, CN или GB
        $table->string('city')->nullable();
        $table->timestamps();
    });
	
Schema::table('users', function (Blueprint $table) {
        // nullable, так как админы Pinnacle могут быть без привязки к агенту
        $table->foreignId('agent_id')->nullable()->after('id')->constrained('agents')->onDelete('set null');
    });
	*/
    Schema::create('shipments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('agent_id')->constrained()->onDelete('cascade');
        $table->foreignId('user_id')->constrained()->onDelete('cascade');

        // Основные ссылки
        $table->string('agent_reference')->unique(); // NSZX07251595
        $table->string('shippers_reference')->nullable(); // N/A
        $table->string('mbl')->nullable(); // Master Bill of Lading

        // Стороны (Parties)
        $table->string('shipper_name');
        $table->string('consignee_name');
        $table->string('pinnacle_office')->default('PININTLCS');

        // Детали перевозки (BLCollection)
        $table->string('mode')->default('LCL'); // LCL, FCL
        $table->string('incoterms')->default('FOB');
        $table->string('vessel')->nullable(); // EVER AEON
        $table->string('voyage')->nullable(); // 1357-005W
        $table->string('origin_port')->nullable(); // CNYTN
        $table->string('destination_port')->nullable(); // GBFXT
        
        // Даты
        $table->date('etd')->nullable(); // 2025-07-22
        $table->date('eta')->nullable(); // 2025-08-24

        // Груз (PackingCollection)
        $table->integer('qty_value')->default(0); // 1001
        $table->string('qty_type')->default('CTN'); // Cartons
        $table->decimal('weight_value', 10, 2)->default(0); // 5893.00
        $table->decimal('volume_qty', 10, 3)->default(0); // 16.723
        $table->string('container_number')->nullable(); // DFSU7302644

        // Статус и логи
        $table->string('status')->default('pending'); 
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
