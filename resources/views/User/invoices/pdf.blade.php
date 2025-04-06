<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture #{{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 20px; }
        .details, .items { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Facture #{{ $invoice->invoice_number }}</h1>
    </div>

    <div class="details">
        <p><strong>Émise le :</strong> {{ $invoice->issued_at->format('d/m/Y H:i') }}</p>
        <p><strong>Sous-total :</strong> ${{ number_format($invoice->subtotal, 2) }}</p>
        <p><strong>Réduction :</strong> ${{ number_format($invoice->discount, 2) }}</p>
        <p><strong>Total :</strong> ${{ number_format($invoice->total, 2) }}</p>
        <p><strong>Méthode de paiement :</strong> {{ $invoice->payment_method }}</p>
        <p><strong>ID de transaction :</strong> {{ $invoice->payment_id }}</p>
    </div>

    <div class="items">
        <h3>Articles</h3>
        <table>
            <thead>
                <tr>
                    <th>Cours</th>
                    <th>Prix</th>
                    <th>Réduction</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @if (is_array($invoice->items) && !empty($invoice->items))
                    @foreach ($invoice->items as $item)
                        <tr>
                            <td>{{ $item['course_title'] }}</td>
                            <td>${{ number_format($item['price'], 2) }}</td>
                            <td>${{ number_format($item['discount'], 2) }}</td>
                            <td>${{ number_format($item['price'] - $item['discount'], 2) }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="4">Aucun article disponible</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</body>
</html>