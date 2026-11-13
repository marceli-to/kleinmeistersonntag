<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('attendees', function (Blueprint $table) {
			$table->id();
			$table->foreignId('event_id')->constrained()->cascadeOnDelete();
			$table->string('firstname');
			$table->string('lastname');
			$table->string('email');
			$table->boolean('attending');
			$table->string('token', 64)->index();
			$table->timestamps();
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('attendees');
	}
};
