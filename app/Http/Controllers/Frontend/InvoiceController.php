<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function success(Request $request)
    {
        $invoice = $request->session()->get('invoice');
        if (!$invoice || $invoice->user_id !== Auth::id()) {
            abort(403, 'Vous n\'êtes pas autorisé à voir cette facture.');
        }

        return view('User.checkout.success', compact('invoice'));
    }

   public function download(Invoice $invoice)
    {
        if ($invoice->user_id !== Auth::id()) {
            abort(403, 'Vous n\'êtes pas autorisé à télécharger cette facture.');
        }

        // Forcer le décodage de items si ce n'est pas déjà un tableau
        if (!is_array($invoice->items)) {
            $invoice->items = json_decode($invoice->items, true);
        }

        $pdf = Pdf::loadView('User.invoices.pdf', compact('invoice'));
        return $pdf->download('facture_' . $invoice->invoice_number . '.pdf');
    }
}