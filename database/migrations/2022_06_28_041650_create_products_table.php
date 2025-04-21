<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // 1. Crear una nueva tabla temporal con la estructura deseada
        Schema::create('new_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('desc');
            $table->unsignedBigInteger('base_price')->default(0);
            $table->unsignedBigInteger('base_stock')->default(0);
            $table->boolean('has_variants')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Copiar los datos de la tabla antigua a la nueva
        DB::statement("
            INSERT INTO new_products 
            (id, category_id, title, desc, base_price, base_stock, created_at, updated_at)
            SELECT id, category_id, title, desc, price, stock, created_at, updated_at
            FROM products
        ");

        // 3. Generar slugs basados en el título
        DB::table('new_products')->update([
            'slug' => DB::raw("LOWER(REPLACE(REPLACE(REPLACE(title, ' ', '-'), 'ñ', 'n'), 'áéíóú', 'aeiou'))")
        ]);

        // 4. Eliminar la tabla antigua y renombrar la nueva
        Schema::dropIfExists('products');
        Schema::rename('new_products', 'products');
    }

    public function down()
    {
        // 1. Crear tabla temporal con estructura original
        Schema::create('old_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->string('title');
            $table->text('desc');
            $table->bigInteger('price');
            $table->bigInteger('stock');
            $table->timestamps();
        });

        // 2. Copiar datos
        DB::statement("
            INSERT INTO old_products 
            (id, category_id, title, desc, price, stock, created_at, updated_at)
            SELECT id, category_id, title, desc, base_price, base_stock, created_at, updated_at
            FROM products
        ");

        // 3. Eliminar y renombrar
        Schema::dropIfExists('products');
        Schema::rename('old_products', 'products');
    }
};