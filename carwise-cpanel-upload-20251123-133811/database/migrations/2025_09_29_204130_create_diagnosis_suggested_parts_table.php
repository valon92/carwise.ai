<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('diagnosis_suggested_parts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diagnosis_result_id')->constrained('diagnosis_results')->onDelete('cascade');
            $table->foreignId('car_part_id')->constrained('car_parts')->onDelete('cascade');
            
            // AI suggestion metadata
            $table->string('suggestion_reason'); // Why this part was suggested
            $table->integer('priority')->default(1); // 1=high, 2=medium, 3=low
            $table->decimal('relevance_score', 3, 2)->default(0.00); // AI relevance score (0.00-1.00)
            $table->boolean('is_required')->default(false); // Is this part required for the fix
            $table->boolean('is_recommended')->default(true); // Is this part recommended
            $table->boolean('is_alternative')->default(false); // Is this an alternative option
            
            // Quantity and usage
            $table->integer('quantity_needed')->default(1);
            $table->string('usage_notes')->nullable(); // How this part is used in the repair
            
            // Cost estimation
            $table->decimal('estimated_cost', 10, 2)->nullable(); // Estimated cost for this part
            $table->string('cost_currency', 3)->default('USD');
            $table->text('cost_breakdown')->nullable(); // Detailed cost breakdown
            
            // Installation information
            $table->integer('estimated_installation_time')->nullable(); // Minutes
            $table->string('installation_difficulty')->default('medium'); // easy, medium, hard
            $table->text('installation_notes')->nullable(); // Installation instructions
            
            // User interaction
            $table->boolean('user_selected')->default(false); // Did user select this part
            $table->boolean('user_purchased')->default(false); // Did user purchase this part
            $table->timestamp('purchased_at')->nullable();
            $table->text('user_notes')->nullable(); // User notes about this part
            
            $table->timestamps();
            
            // Indexes
            $table->index(['diagnosis_result_id', 'priority']);
            $table->index(['car_part_id', 'is_required']);
            $table->index(['relevance_score', 'is_recommended']);
            $table->unique(['diagnosis_result_id', 'car_part_id']); // Prevent duplicates
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diagnosis_suggested_parts');
    }
};