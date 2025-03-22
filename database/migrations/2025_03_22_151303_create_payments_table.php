<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // ID de l'utilisateur qui a payé
            $table->decimal('amount', 10, 2); // Montant total payé
            $table->string('stripe_payment_id'); // ID de la transaction Stripe
            $table->string('status')->default('paid'); // Statut du paiement (paid par défaut avec Stripe)
            $table->string('invoice')->nullable(); // Chemin du fichier facture (PDF)
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
}